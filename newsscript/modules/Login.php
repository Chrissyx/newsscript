<?php
namespace com\chrissyx\newsscript;

/**
 * Manages the login, request of new password and logout.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Login extends PublicModule
{
    use Mode;

    /**
     * Entered user name for logging in.
     *
     * @var string Login user name
     */
    private $userName;

    /**
     * Entered password for logging in.
     *
     * @var string Login password
     */
    private $password;

    /**
     * Choice to stay logged in after session expired.
     *
     * @var bool Stay logged in
     */
    private $stayLoggedIn;

    /**
     * Sets submitted login data and mode to execute.
     *
     * @return Login New instance of this class
     */
    protected function __construct()
    {
        parent::__construct();
        $this->mode = Functions::getFromGlobals('mode');
        $this->userName = Functions::getFromGlobals('userName');
        $this->password = Functions::getFromGlobals('password');
        $this->stayLoggedIn = Functions::getFromGlobals('stayLoggedIn') == 'true';
    }

    /**
     * Performs login, sending new password or logout.
     */
    public function publicCall()
    {
        if(Auth::getInstance()->isLoggedIn() && $this->mode != 'logout')
            return AdminIndex::getInstance()->publicCall();
        Title::getInstance()->addSubTitle(Language::getInstance()->getString('login'));
        switch($this->mode)
        {
            case 'request':
            Title::getInstance()->addSubTitle(Language::getInstance()->getString('request_new_password'));
            Template::getInstance()->printPage('RequestPassword');
            break;

            case 'logout':
            Auth::getInstance()->logout();
            Template::getInstance()->redir('index.php');
            break;

            case 'login':
            if(empty($this->userName))
                Messages::getInstance()->addMessage(Messages::WARNING, Language::getInstance()->getString('please_enter_your_user_name'));
            if(empty($this->password))
                Messages::getInstance()->addMessage(Messages::WARNING, Language::getInstance()->getString('please_enter_your_password'));
            if(!Messages::getInstance()->hasMessages())
                Auth::getInstance()->login($this->userName, Functions::getHash($this->password), $this->stayLoggedIn, false);
            if(!Messages::getInstance()->hasMessages())
                Template::getInstance()->redir('index.php');

            default:
            Template::getInstance()->printPage('Login', array('userName' => $this->userName,
                'password' => $this->password,
                'stayLoggedIn' => $this->stayLoggedIn));
            break;
        }
    }
}
?>