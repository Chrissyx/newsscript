<?php
/**
 * Newsticker für interne, externe und RSS Anbindung.
 *
 * @author Chrissyx
 * @copyright (c) 2001-2022 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @link http://www.rssboard.org/
 * @package CHS_Newsscript
 * @version 1.0.7
 */
file_exists('newsscript/settings.php') ? include('newsscript/settings.php') : list($newsdat, , , $newscomments, , , $smilies, , , , $tickermax, $redir, , $bbcode1, $bbcode2, $lang['news']['DATEFORMAT']) = @array_map('trim', array_merge(array_slice(explode("\n", file_get_contents('newsscript/settings.dat.php')), 1), array('/(.*)/', '\1', 'd.m.Y'))) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
$news = array_map('trim', file($newsdat)) or die('<b>ERROR:</b> News nicht gefunden!');
$size = count($news = array_slice($news, 1));
$size = intval(isset($_GET['anz']) && $_GET['anz'] > $size ? $size : (!isset($_GET['anz']) ? ($tickermax > $size ? $size : $tickermax) : $_GET['anz']));
$link = 'http://' . str_replace('//', '/', $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/');
if(file_exists('newsscript/smilies.php'))
    include('newsscript/smilies.php');
include('newsscript/language_news.php');

switch(isset($_GET['type']) ? $_GET['type'] : '')
{
    case 'rss': //RSS V2.0.10
    include('newsscript/cats.php');
    header('Content-Type: application/rss+xml');
    echo('<?xml version="1.0" encoding="' . $lang['news']['charset'] .'" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
 <channel>
  <title>' . $_SERVER['SERVER_NAME'] . ' RSS Newsfeed</title>
  <link>' . ($redir ? $redir : $link) . '</link>
  <description>' . $lang['news']['newsfrom'] .' http://' . $_SERVER['SERVER_NAME'] . '/</description>
  <language>' . $lang['news']['code'] .'</language>
  <lastBuildDate>' . date('r', current(sscanf(current($news), "%*d\t%d"))) . '</lastBuildDate>
  <pubDate>' . date('r', current(sscanf(end($news), "%*d\t%d"))) . '</pubDate>
  <docs>http://www.rssboard.org/rss-specification</docs>
  <generator>CHS - Newsscript</generator>
  <atom:link href="' . $link . 'newsticker.php?type=rss" rel="self" type="application/rss+xml" />
');
    for($i=0; $i<$size; $i++)
    {
        $value = explode("\t", $news[$i]);
        echo('  <item>
   <title>' . preg_replace($bbcode1, $bbcode3, $value[5]) . '</title>
   <link>' . ($redir ? $redir : $link . 'news.php') . '?newsid=' . $value[0] . '</link>
   <guid isPermaLink="true">' . ($redir ? $redir : $link . 'news.php') . '?newsid=' . $value[0] . '</guid>
   <pubDate>' . date('r', $value[1]) . '</pubDate>
   <dc:creator>' . $value[3] . '</dc:creator>
   <category>' . (!empty($value[4]) ? $cats[$value[4]][0] : '') . '</category>
   <description>' . (!empty($value[4]) && $cats[$value[4]][1] ? '&lt;img src=&quot;' . $cats[$value[4]][1] . '&quot; alt=&quot;' . $cats[$value[4]][0] . '&quot; style=&quot;float:right;&quot;&gt;'  : '') . htmlspecialchars(preg_replace($bbcode1, $bbcode2, is_array($smilies) ? strtr($value[7], $smilies) : $value[7])) . '</description>
   <comments>' . ($redir ? $redir : $link . 'news.php') . '?newsid=' . $value[0] . '#box</comments>
   <slash:comments>' . (file_exists($newscomments . $value[0] . '.dat') ? count(file($newscomments . $value[0] . '.dat')) : '0') . '</slash:comments>
  </item>
');
    }
    echo(' </channel>
</rss>');
    break;

    case 'extern':
    echo("document.write('<!-- CHS - Newsscript - Ticker Start -->');\n");
    for($i=0; $i<$size; $i++)
    {
        $value = explode("\t", $news[$i]);
        echo('document.write(\'' . date($lang['news']['DATEFORMAT'], $value[1]) . ': <a href="' . ($redir ? $redir : $link . 'news.php') . '?newsid=' . $value[0] . '" target="_blank">' . $value[5] . "</a><br />');\n");
    }
    echo("document.write('<!-- /CHS - Newsscript - Ticker Ende -->');\n");
    break;

    default:
    for($i=0; $i<$size; $i++)
    {
        $value = explode("\t", $news[$i]);
        echo(date($lang['news']['DATEFORMAT'], $value[1]) . ': <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '">' . preg_replace("/<a .*?>(.*?)<\/a>/si", '\1', preg_replace($bbcode1, $bbcode2, is_array($smilies) ? strtr($value[5], $smilies) : $value[5])) . "</a><br />\n");
    }
    break;
}
?>