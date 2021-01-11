<?php
namespace com\chrissyx\newsscript;

/**
 * Manages document title and RSS feed meta tags.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package AchStats
 */
class Title
{
    use Singleton;

    /**
     * Current added elements of document title.
     *
     * @var array Contains title elements, at least the basic title
     */
    private $titles = array('CHS Newsscript');

    /**
     * Current added elements of RSS feed meta tags.
     *
     * @var array Contains RSS feed meta tags
     */
    private $rssFeeds = array();

    /**
     * Adds new RSS feed meta tag to the document header.
     *
     * @param string $action Action part after the base URL up to the type
     * @param string $lngKey,... Key of translation to display with optional arguments
     */
    public function addRssFeed($action, $lngKey)
    {
        $this->rssFeeds[] = array('title' => vsprintf(Language::getInstance()->getString($lngKey), array_slice(func_get_args(), 2)), 'action' => $action);
    }

    /**
     * Adds new sub-title(s) to the document title.
     *
     * @param string,... $subTitle Sub-title(s) to add
     */
    public function addSubTitle($subTitle)
    {
        $this->titles[] = htmlspecialchars($subTitle, ENT_QUOTES);
        if(func_num_args() > 1)
            foreach(array_slice(func_get_args(), 1) as $curSubTitle)
                $this->titles[] = htmlspecialchars($curSubTitle, ENT_QUOTES);
    }

    /**
     * Returns  all current added RSS feed meta tag(s).
     *
     * @return string RSS feed meta tags for document header
     */
    public function getRssFeed()
    {
        return $this->rssFeeds;
    }

    /**
     * Returns full document title compromised of all current added elements.
     *
     * @param string $spacer Spacer to use for joining title parts
     * @return string Full document title
     */
    public function getTitle($spacer=' » ')
    {
        return implode($spacer, $this->titles);
    }
}
?>