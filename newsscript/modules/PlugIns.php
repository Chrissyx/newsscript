<?php
namespace com\chrissyx\newsscript;
use \Exception as Exception;
use \ReflectionClass as ReflectionClass;

/**
 * Plug-in controller for caching, loading and calling all found plug-ins hooking into executing of the Newsscript.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class PlugIns
{
    use Singleton;

    const HOOK_CONFIG_INIT = 'HOOK_CONFIG_INIT';

    const HOOK_CORE_INIT = 'HOOK_CORE_INIT';
    const HOOK_CORE_RUN = 'HOOK_CORE_RUN';
    const HOOK_CORE_MODULE_CALL = 'HOOK_CORE_MODULE_CALL';

    const HOOK_DB_INIT = 'HOOK_DB_INIT';

    const HOOK_NEWS_INIT = 'HOOK_NEWS_INIT';

    const HOOK_TEMPLATE_INIT = 'HOOK_TEMPLATE_INIT';
    const HOOK_TEMPLATE_PAGE = 'HOOK_TEMPLATE_PAGE';

    /**
     * Loaded plug-in instances.
     *
     * @var array Loaded plug-ins
     */
    private $plugIns = array();

    /**
     * Detected official hooks as provided by this controller.
     *
     * @var array Official hook names
     */
    private $officialHooks;

    /**
     * Already called hooks during one execution run to prevent plug-ins called again.
     *
     * @var array Processed hook names
     */
    private $calledHooks = array();

    /**
     * Loads all found / cached plug-ins and detects official hooks.
     *
     * @return PlugIns New instance of this class
     */
    private function __construct()
    {
        if(file_exists('cache/plugIns.php'))
            include('cache/plugIns.php');
        else
        {
            $plugInsCache = "<?php\n";
            foreach(Functions::glob('plugins/*.php') as $curPlugIn)
            {
                //Detect namespace + class name of current plug-in
                $curDeclaredClasses = get_declared_classes();
                include($curPlugIn);
                $curDeclaredClasses = array_diff(get_declared_classes(), $curDeclaredClasses);
                //Check for valid class
                if(count($curDeclaredClasses) != 1)
                {
                    Logger::getInstance()->warn('Plug-in "%s" has defined invalid number of classes, loading skipped!', $curPlugIn);
                    continue;
                }
                $curPlugInClass = current($curDeclaredClasses);
                //Check for interface
                $curReflectionClass = new ReflectionClass($curPlugInClass);
                if(!$curReflectionClass->implementsInterface(__NAMESPACE__ . '\\PlugIn'))
                {
                    Logger::getInstance()->warn('Plug-in "%s" does not implement required interface, loading skipped!', $curPlugIn);
                    continue;
                }
                //Use full class path for creating instance
                $this->plugIns[] = new $curPlugInClass();
                $plugInsCache .= 'include(\'' . $curPlugIn . '\'); $this->plugIns[] = new ' . $curPlugInClass . "();\n";
            }
            file_put_contents('cache/plugIns.php', $plugInsCache . '?>', LOCK_EX);
        }
        //Set official hook names
        $curReflectionClass = new ReflectionClass($this);
        $this->officialHooks = array_values($curReflectionClass->getConstants());
    }

    /**
     * Calls registered plug-ins on given hook.
     *
     * @param string $hook Official or custom hook name
     * @return Hook was dispatched among all registered plug-ins
     */
    public function callHook($hook)
    {
        //Hook already called before?
        if(in_array($hook, $this->calledHooks))
        {
            Logger::getInstance()->warn('Script "%s" tried to call hook "%s" again!', debug_backtrace(null, 1)[0]['file'], $hook);
            return false;
        }
        //Mark hook as called first and dispatch afterwards
        $this->calledHooks[] = $hook;
        foreach($this->plugIns as $curPlugIn)
            try
            {
                $curPlugIn->onHook($hook, in_array($hook, $this->officialHooks));
            }
            catch(Exception $e)
            {
                Logger::getInstance()->error('Plug-in "%s" failed execution on called hook "%s": %s', get_class($curPlugIn), $hook, $e->getMessage());
            }
        return true;
    }

    /**
     * Returns loaded plug-ins.
     *
     * @return array Current active plug-ins
     */
    public function getPlugIns()
    {
        return $this->plugIns;
    }
}
?>