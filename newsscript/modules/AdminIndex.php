<?php
namespace com\chrissyx\newsscript;

class AdminIndex extends PublicModule
{
    use Mode;

    protected function __construct()
    {
        parent::__construct();
        $this->mode = Functions::getFromGlobals('mode');
    }

    public function publicCall()
    {
        if(!Auth::getInstance()->isLoggedIn())
            return Login::getInstance()->publicCall();
        switch($this->mode)
        {
            default:
            Template::getInstance()->printPage('AdminIndex');
            break;
        }
    }
}
?>