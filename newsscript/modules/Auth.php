<?php
namespace com\chrissyx\newsscript;
use \PDO as PDO;

/**
 * Authentication, login state and data of current user.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Auth
{
    use Singleton;

    private $ipAddressHash;

    /**
     * Loaded user data if logged in.
     *
     * @var array User data
     */
    private $userData = array();

    /**
     * Checks for a possible login.
     *
     * @return Auth New instance of this class
     */
    function __construct()
    {
        if(DB::getInstance()->exec('CREATE TABLE IF NOT EXISTS users ("userId" INTEGER NOT NULL, "userName" VARCHAR(511) NOT NULL, "passwordHash" VARCHAR(1023) NOT NULL, "email" VARCHAR(511) NOT NULL, PRIMARY KEY ("userId"))') != 0)
            Logger::getInstance()->info('Users table created');
        if(DB::getInstance()->exec('CREATE TABLE IF NOT EXISTS logins ("ipAddressHash" VARCHAR(1023) NOT NULL, "fails" INTEGER NOT NULL, "lastTry" INTEGER NOT NULL, PRIMARY KEY ("ipAddressHash"))') != 0)
            Logger::getInstance()->info('Logins table created');
        $this->ipAddressHash = Functions::getHash($_SERVER['REMOTE_ADDR']);
        //Check session-based login
        if(isset($_SESSION['userName'], $_SESSION['passwordHash']))
            $this->login($_SESSION['userName'], $_SESSION['passwordHash'], false, true);
        //Check cookie-based login
        elseif(isset($_COOKIE['stayLoggedInUser']))
        {
            $stayLoggedInUser = explode("\t", $_COOKIE['stayLoggedInUser']);
            $this->login($stayLoggedInUser[0], $stayLoggedInUser[1], false, true);
        }
    }

    /**
     * Returns login state of user.
     *
     * @return bool User logged in
     */
    public function isLoggedIn()
    {
        return !empty($this->userData);
    }

    /**
     * Tries to login with the given account credentials. Uses {@link Messages} in case of failure.
     *
     * @param string $userName Name of user account
     * @param string $passwordHash Hashed password of user account
     * @param bool $stayLoggedIn Stay logged in after session expires
     * @param bool $relogin Automatic relogin from session or cookie
     */
    public function login($userName, $passwordHash, $stayLoggedIn, $relogin)
    {
        $fails = (int) DB::getInstance()->query('SELECT "fails" FROM logins WHERE "ipAddressHash" = %s AND "lastTry" > %s', $this->ipAddressHash, time()-60*60*24)->fetchColumn();
        if($fails > 4)
            return Messages::getInstance()->addMessage(Messages::ERROR, Language::getInstance()->getString('login_trys_exceeded'));
        $this->userData = DB::getInstance()->query('SELECT * FROM users WHERE "userName" = %s AND "passwordHash" = %s', $userName, $passwordHash)->fetch(PDO::FETCH_ASSOC) ?: array();
        if($this->isLoggedIn())
        {
            DB::getInstance()->exec('DELETE FROM logins WHERE "ipAddressHash" = %s', $this->ipAddressHash);
            //Login session-based
            $_SESSION['userName'] = $this->userData['userName'];
            $_SESSION['passwordHash'] = $this->userData['passwordHash'];
            //Login cookie-based
            if(!$relogin)
            {
                setcookie('stayLoggedInUser', $this->userData['userName'] . "\t" . $this->userData['passwordHash'], $stayLoggedIn ? time()+60*60*24*365 : 0);
                Logger::getInstance()->info('%s logged in', $this->userData['userName']);
            }
        }
        else
        {
            Logger::getInstance()->info('Failed login for user name "%s"', $userName);
            Messages::getInstance()->addMessage(Messages::ERROR, Language::getInstance()->getString('wrong_user_name_or_password'));
            DB::getInstance()->exec($fails != 0 ? 'UPDATE logins SET "fails" = "fails"+1, "lastTry" = %1$s WHERE "ipAddressHash" = %2$s' : 'INSERT INTO logins VALUES (%2$s, 1, %1$s)', time(), $this->ipAddressHash);
        }
    }

    /**
     * Logs current logged in user out.
     */
    public function logout()
    {
        Logger::getInstance()->info('%s logged out', $this->userData['userName']);
        $this->userData = array();
        unset($_SESSION['userName'], $_SESSION['passwordHash']);
        setcookie('stayLoggedInUser', '', time()-1000);
    }
}
?>