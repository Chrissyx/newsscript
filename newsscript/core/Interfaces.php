<?php
namespace com\chrissyx\newsscript;

/**
 * API definition for a plug-in.
 */
interface PlugIn
{
    /**
     * Returns description of this plug-in.
     *
     * @return string Description of plug-in
     */
    public function getDescription();

    /**
     * Returns name of this plug-in.
     *
     * @return string Name of plug-in
     */
    public function getName();

    /**
     * Called on the specified hook. Hooks can be official ones from the system or custom calls e.g. from other plug-ins.
     *
     * @param string $hook Hook name
     * @param bool $official Hook being an official one
     */
     public function onHook($hook, $official);
}
?>