<?php
namespace com\chrissyx\newsscript;
use \PDO as PDO;

class News extends PublicModule
{
    use Mode;

    protected function __construct()
    {
        parent::__construct();
        $this->mode = Functions::getFromGlobals('mode');
        PlugIns::getInstance()->callHook(PlugIns::HOOK_NEWS_INIT);
    }

    public function publicCall()
    {
        switch($this->mode)
        {
            default:
            Template::getInstance()->assign('encoding', DB::getInstance()->query('PRAGMA encoding')->fetch(PDO::FETCH_ASSOC)['encoding']);
            Template::getInstance()->printPage('News');
            break;
        }
    }
}
?>