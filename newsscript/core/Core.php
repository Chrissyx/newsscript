<?php
namespace com\chrissyx\newsscript;
use \Exception as Exception;

/**
 * Core and entry point of the newsscript.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Core
{
    use Singleton;

    /**
     * Indicates the used locale is based on UTF-8 and stuff like month names don't need {@link utf8_encode()}.
     *
     * @var bool UTF-8 based locale loaded
     */
    private $utf8Locale;

    /**
     * Some initial PHP configurations and internal settings.
     *
     * @return Core New instance of this class
     */
    private function __construct()
    {
        //Error handling
        error_reporting(E_ALL);
        set_exception_handler(function(Exception $e)
        {
            Logger::getInstance()->error(get_class($e) . ': ' . $e->getMessage());
            echo($e);
        });
        //Load feature sets
        if(file_exists('cache/serverHash.php'))
            $serverHash = include('cache/serverHash.php');
        if(!isset($serverHash) || $serverHash != $this->getServerHash())
        {
            file_put_contents('cache/serverHash.php', '<?php return \'' . $this->getServerHash() . '\'; ?>', LOCK_EX);
            //Changed server environment detected, create new config
            file_put_contents('cache/serverConfig.php', '<?php '
                //Finalize feature set of Functions class by either using Multibyte string functions and/or (overloaded) default PHP ones
                . 'require(\'core/Functions' . (!extension_loaded('mbstring') || (extension_loaded('mbstring') && ini_set('mbstring.func_overload', '7') !== false) ? '' : 'Mb') . '.php\'); '
                //Provide password_hash() function and its constant in case of PHP < 5.5
                . (!function_exists('password_hash') ? 'define(\'PASSWORD_BCRYPT\', 0); function password_hash($password, $algo) { return ' . (function_exists('hash') ? 'hash(\'sha512\', $password)' : 'crypt($password)') . '; } ' : '')
                . '?>', LOCK_EX);
        }
        require('cache/serverConfig.php');
        //Set locale for dates and number formats
        $this->utf8Locale = Functions::stripos(setlocale(LC_ALL, explode(',', Language::getInstance()->getString('LOCALES', 'Main'))), '.utf8') !== false;
        //Initialization done
        PlugIns::getInstance()->callHook(PlugIns::HOOK_CORE_INIT);
    }

    /**
     * Returns the used locale is based on UTF-8.
     *
     * @return bool UTF-8 based locale loaded
     */
    public function isUtf8Locale()
    {
        return $this->utf8Locale;
    }

    /**
     * Runs the action or the default one with accompanied output services.
     */
    public function run()
    {
        PlugIns::getInstance()->callHook(PlugIns::HOOK_CORE_RUN);
        //Manage output compression
        if(Config::getInstance()->getConfigVal('output_compression') && ini_get('zlib.output_compression') != '1' && ini_get('output_handler') != 'ob_gzhandler')
            ob_start('ob_gzhandler');
        //Manage session
        session_name('sid');
        if(session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if(session_id() == '0')
            session_regenerate_id();
        //Provide session IDs
        if(SID != '')
        {
            //URL-based
            define('SID_QMARK', '?' . htmlspecialchars(SID));
            define('SID_AMPER', '&amp;' . htmlspecialchars(SID));
            define('SID_AMPER_RAW', '&' . htmlspecialchars(SID));
        }
        else
        {
            //Cookie-based
            define('SID_QMARK', '');
            define('SID_AMPER', '');
            define('SID_AMPER_RAW', '');
        }
        //Load the specific module
        $action = __NAMESPACE__ . '\\' . (ucfirst(Functions::getFromGlobals('action')) ?: 'News');
        if(!class_exists($action) || !is_subclass_of($action, __NAMESPACE__ . '\\' . 'PublicModule'))
            $action = 'News';
        PlugIns::getInstance()->callHook(PlugIns::HOOK_CORE_MODULE_CALL);
        $action::getInstance()->publicCall();
    }

    /**
     * Returns the hash from the current server environment.
     *
     * @return string Hash considering PHP version, all PHP settings and loaded extensions
     */
    private function getServerHash()
    {
        return md5(PHP_VERSION . serialize(ini_get_all(null, false) + get_loaded_extensions()));
    }
}
?>