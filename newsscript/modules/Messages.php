<?php
namespace com\chrissyx\newsscript;

/**
 * Controls messages with their classifications.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Messages
{
    use Singleton;

    /**
     * Classifies a message as successful.
     */
    const SUCCESS = 0;

    /**
     * Classifies a message as an information.
     */
    const INFO = 1;

    /**
     * Classifies a message as a warning.
     */
    const WARNING = 2;

    /**
     * Classifies a message as an error.
     */
    const ERROR = 3;

    /**
     * Contains all added messages keyed by their classifier.
     *
     * @var array Added messages
     */
    private $messages = array();

    /**
     * Adds new message consisting of its classification and text to display.
     *
     * @param int $classifier Predefined classifier
     * @param string $text Message text
     */
    public function addMessage($classifier, $text)
    {
        $this->messages[$classifier][] = $text;
    }

    /**
     * Returns all added messages keyed by their classifier.
     *
     * @return Added messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Returns messages have been added.
     *
     * @return bool Messages available
     */
    public function hasMessages()
    {
        return !empty($this->messages);
    }
}
?>