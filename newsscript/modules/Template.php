<?php
namespace com\chrissyx\newsscript;
use \Smarty as Smarty;

/**
 * Inits Smarty, manages configuration, assigns values to template files and prints pages.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
if(Config::getInstance()->getConfigVal('smarty_dir') != '')
    //Do not use Composer's autoloader having a differing Smarty installation
    require_once(Config::getInstance()->getConfigVal('smarty_dir') . '/Smarty.class.php');
/**
 * Wrapper class for Smarty 3 API.
 */
class Template
{
    use Singleton;

    /**
     * The Smarty object to work with.
     *
     * @var Smarty Smarty instance
     */
    private $smarty;

    /**
     * Folder names of available templates.
     *
     * @var array Available templates
     */
    private $availableTpls;

    /**
     * Directory of used template.
     *
     * @var string Template folder
     */
    private $tplDir;

    /**
     * Sets up Smarty instance.
     *
     * @return Template New instance of this class
     */
    function __construct()
    {
        if(file_exists('cache/templates.php'))
            $this->availableTpls = include('cache/templates.php');
        else
        {
            $this->availableTpls = array_map('basename', Functions::glob('templates/*/'));
            file_put_contents('cache/templates.php', '<?php return array(\'' . implode('\', \'', $this->availableTpls) . '\'); ?>', LOCK_EX);
        }
        $this->tplDir = 'templates/' . Config::getInstance()->getConfigVal('tpl_dir') . '/';
        $this->smarty = new Smarty();
        //Settings
        $this->smarty->setErrorUnassigned(error_reporting() == E_ALL);
        $this->smarty->setCacheDir('cache/')
            ->setCompileDir('cache/')
            ->setTemplateDir($this->tplDir)
            ->addPluginsDir('modules/Template/plugins/');
        //Register namespaced modules for usage in templates
        foreach(Functions::glob('{core,modules}/*.php', GLOB_BRACE) as $curModule)
        {
            $curModule = basename($curModule, '.php');
            $this->smarty->registered_classes[$curModule] = __NAMESPACE__ . '\\' . $curModule;
/*
            if(class_exists(__NAMESPACE__ . '\\' . $curModule, false))
                $this->smarty->registerClass($curModule, __NAMESPACE__ . '\\' . $curModule);
*/
        }
        PlugIns::getInstance()->callHook(PlugIns::HOOK_TEMPLATE_INIT);
    }

    /**
     * Assigns value(s) to Smarty.
     *
     * @param mixed $tplVar Name of value or array with name+value pairs
     * @param mixed $value Value for single var
     */
    public function assign($tplVar, $value=null)
    {
        $this->smarty->assign($tplVar, $value);
    }

    /**
     * Assigns a single value to Smarty by reference.
     *
     * @param mixed $tplVar Name of value
     * @param mixed $value Value reference
     */
    public function assignByRef($tplVar, &$value)
    {
        $this->smarty->assignByRef($tplVar, $value);
    }

    /**
     * Clears the entire Smarty cache.
     *
     * @return int Amount of deleted files
     */
    public function clearCache()
    {
        return $this->smarty->clearAllCache();
    }

    /**
     * Displays a template file and assigns optional values prior to it.
     *
     * @param string $tplName Name of template file
     * @param mixed $tplVar Name of single value or array with name+value pairs
     * @param mixed $value Value for single var
     */
    public function display($tplName, $tplVar=null, $value=null)
    {
        if(!empty($tplVar))
            $this->assign($tplVar, $value);
        $this->smarty->display($tplName . '.tpl');
    }

    /**
     * Returns fetched contents (with assigned data) of a template file.
     *
     * @param string $tplName Name of template file
     * @param mixed $tplVar Name of single value or array with name+value pairs
     * @param mixed $value Value for single var
     * @return string Rendered template output
     */
    public function fetch($tplName, $tplVar=null, $value=null)
    {
        if(!empty($tplVar))
            $this->assign($tplVar, $value);
        return $this->smarty->fetch($tplName . '.tpl');
    }

    /**
     * Returns available templates as technical names.
     *
     * @return array Available templates
     */
    public function getAvailableTpls()
    {
        return $this->availableTpls;
    }

    /**
     * Returns used template directory.
     *
     * @return string Used template folder
     */
    public function getTplDir()
    {
        return $this->tplDir;
    }

    /**
     * Prints the head of a page.
     */
    public function printHeader()
    {
        //Clickjacking protection
        if(Config::getInstance()->getConfigVal('clickjacking'))
            header('X-FRAME-OPTIONS: SAMEORIGIN');
        $this->display('PageHeader');
    }

    /**
     * Prints a full page message and exits program execution.
     *
     * @param string $msgIndex Identifier part of message title and text
     * @param mixed $args,... Optional arguments to be replaced in message text
     */
    public function printMessage($msgIndex, $args=null)
    {
        PlugIns::getInstance()->callHook(PlugIns::HOOK_TEMPLATE_PAGE);
        $this->printHeader();
        $this->display('Message', array('msgTitle' => Language::getInstance()->getString('title_' . $msgIndex),
            'msgText' => vsprintf(Language::getInstance()->getString('text_' . $msgIndex), array_slice(func_get_args(), 1))));
        exit($this->printTail());
    }

    /**
     * Prints a full page with provided template file, optional values to assign before and exits program execution.
     *
     * @param string $tplName Name of template file
     * @param mixed $tplVar Name of single value or array with name+value pairs
     * @param mixed $value Value for single var
     */
    public function printPage($tplName, $tplVar=null, $value=null)
    {
        PlugIns::getInstance()->callHook(PlugIns::HOOK_TEMPLATE_PAGE);
        if(!empty($tplVar))
            $this->assign($tplVar, $value);
        $this->printHeader();
        $this->display($tplName);
        exit($this->printTail());
    }

    /**
     * Prints the tail of a page.
     */
    public function printTail()
    {
        $this->display('PageTail', array('version' => VERSION,
            'queryCounter' => DB::getInstance()->getQueryCounter(),
            'memoryUsage' => memory_get_usage()/1024));
    }

    /**
     * Redirects to the given URL and displays manual link if sending header failed.
     *
     * @param string $url URL to redirect to
     * @param string $text Optional text to display if redirecting by sending header failed
     */
    public function redir($url, $text=null)
    {
        header('Location: ' . $url);
        exit('<a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($text != null ?: $url) . '</a>');
    }

    /**
     * Tests template engine installation and returns found errors.
     *
     * @return array Reported errors during test run
     */
    public function testTplInstallation()
    {
        $errors = array();
        $this->smarty->testInstall($errors);
        return $errors;
    }
}
?>