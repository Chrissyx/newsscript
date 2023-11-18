<?php
/**
 * Newsmodul zum Anzeigen und Verwalten der News. Verarbeitet auch Login und Passwörter.
 *
 * @author Chrissyx
 * @copyright (c) 2001-2023 by Chrissyx
 * @license https://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Newsscript
 * @version 1.0.7.2
 */
//Caching
if(file_exists('newsscript/settings.php') && (filemtime('newsscript/settings.php') > filemtime('newsscript/settings.dat.php')))
    include_once('newsscript/settings.php');
else
{
    //Config: News, Anzahl, Passwörter, Kommntare, Kategorien, Bilder Ordner, Smilies, Smilie Ordner, Smilies Anzahl, Smilies Anzahl Reihe, Newsticker Anzahl, Redir nach Login, CAPTCHA
    list($newsdat, $newsmax, $newspwsdat, $newscomments, $newscatsdat, $newscatpics, $smilies, $smiliepics, $smiliesmax, $smiliesmaxrow, $tickermax, $redir, $captcha) = @array_map('trim', array_slice(explode("\n", file_get_contents('newsscript/settings.dat.php')), 1)) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
    list($newsmax, $smiliesmax, $smiliesmaxrow, $tickermax) = array_map('intval', array($newsmax, $smiliesmax, $smiliesmaxrow, $tickermax));
    if(($forum = substr($smilies, -4) == '.var' ? implode('/', array_slice(explode('/', $smilies), 0, -2)) : '') != '')
        $forum .= '/';
    $bbcode1 = array("/\[b\](.*?)\[\/b\]/si",
                     "/\[i\](.*?)\[\/i\]/si",
                     "/\[u\](.*?)\[\/u\]/si",
                     "/\[s\](.*?)\[\/s\]/si",
                     "/\[center\](.*?)\[\/center\]/si",
                     "/\[email\](.*?)\[\/email\]/si",
                     "/\[email=(.*?)\](.*?)\[\/email\]/si",
                     "/\[img\](.*?)\[\/img\]/si",
                     "/\[img=(.*?)\](.*?)\[\/img\]/si",
                     "/\[url\](.*?)\[\/url\]/si",
                     "/\[url=(.*?)\](.*?)\[\/url\]/si",
                     "/\[color=(\#[a-fA-F0-9]{6}|[a-zA-Z]+)\](.*?)\[\/color\]/si",
                     "/\[sup\](.*?)\[\/sup\]/si",
                     "/\[sub\](.*?)\[\/sub\]/si",
                     "/\[code\](.*?)\[\/code\]/si",
                     "/\[size=\-2\](.*?)\[\/size\]/si",
                     "/\[size=\-1\](.*?)\[\/size\]/si",
                     "/\[size=\+1\](.*?)\[\/size\]/si",
                     "/\[size=\+2\](.*?)\[\/size\]/si",
                     "/\[size=\+3\](.*?)\[\/size\]/si",
                     "/\[size=\+4\](.*?)\[\/size\]/si",
                     "/\[quote\](.*?)\[\/quote\]/si",
                     "/\[flash\](.*?)\[\/flash\]/si",
                     "/\[flash=(\d+),(\d+)\](.*?)\[\/flash\]/si",
                     "/\[iframe\](.*?)\[\/iframe\]/si",
                     "/\[iframe=(\d+),(\d+)\](.*?)\[\/iframe\]/si",
                     "/\[list\][<br \/>\r\n]*(.*?)\[\/list\]/si",
                     "/\[\*\](.*?)(<br \/>|[\r\n])/si");
    $bbcode2 = array('<span style="font-weight:bold;">\1</span>',
                     '<span style="font-style:italic;">\1</span>',
                     '<span style="text-decoration:underline;">\1</span>',
                     '<span style="text-decoration:line-through;">\1</span>',
                     '<p style="text-align:center;">\1</p>',
                     '<a href="mailto:\1">\1</a>',
                     '<a href="mailto:\1">\2</a>',
                     '<img src="\1" alt="" />',
                     '<img src="\1" alt="\2" title="\2" />',
                     '<a href="\1" target="_blank">\1</a>',
                     '<a href="\1" target="_blank">\2</a>',
                     '<span style="color:\1;">\2</span>',
                     '<sup>\1</sup>',
                     '<sub>\1</sub>',
                     '<code>\1</code>',
                     '<span style="font-size:xx-small;">\1</span>',
                     '<span style="font-size:x-small;">\1</span>',
                     '<span style="font-size:large;">\1</span>',
                     '<span style="font-size:x-large;">\1</span>',
                     '<span style="font-size:xx-large;">\1</span>',
                     '<span style="font-size:300%;">\1</span>',
                     '<blockquote><p style="font-style:italic;">\1</p></blockquote>',
                     '<object data="\1" type="application/x-shockwave-flash" width="425" height="355">
 <param name="allowFullScreen" value="true" />
 <param name="allowScriptAccess" value="sameDomain" />
 <param name="movie" value="\1" />
 <param name="quality" value="autohigh" />
 <param name="wmode" value="transparent" />
 <p>No Flash installed! Please <a href="https://get.adobe.com/flashplayer/" target="_blank">update your browser</a>.</p>
</object>',
                     '<object data="\3" type="application/x-shockwave-flash" width="\1" height="\2">
 <param name="allowFullScreen" value="true" />
 <param name="allowScriptAccess" value="sameDomain" />
 <param name="movie" value="\3" />
 <param name="quality" value="autohigh" />
 <param name="wmode" value="transparent" />
 <p>No Flash installed! Please <a href="https://get.adobe.com/flashplayer/" target="_blank">update your browser</a>.</p>
</object>',
                     '<iframe src="\1" width="560" height="315" frameborder="0">Your browser does not support inline frames. <a href="\1" target="_blank">Click here to open the content in a new window.</a></iframe>',
                     '<iframe src="\3" width="\1" height="\2" frameborder="0">Your browser does not support inline frames. <a href="\3" target="_blank">Click here to open the content in a new window.</a></iframe>',
                     '<ul>
\1</ul>',
                     ' <li>\1</li>
');
    $bbcode3 = array('\1', '\1', '\1', '\1', '\1', '\1', '\2', '\1', '\2', '\1', '\2', '\2', '\1', '\1', '\1', '\1', '\1', '\1', '\1', '\1', '\1', '\1', '\1', '\3', '\1', '\3', '\1', '\1');
    $temp = fopen('newsscript/settings.php', 'w');
    fwrite($temp, "<?php\n//Auto-generated config!\n\$newsdat = '$newsdat';\n\$newsmax = $newsmax;\n\$newspwsdat = '$newspwsdat';\n\$newscomments = '$newscomments';\n\$newscatsdat = '$newscatsdat';\n\$newscatpics = '$newscatpics';\n\$smilies = '$smilies';\n\$smiliepics = '$smiliepics';\n\$smiliesmax = " . ($smiliesmax ? $smiliesmax : "''") . ";\n\$smiliesmaxrow = " . ($smiliesmaxrow ? $smiliesmaxrow : "''") . ";\n\$tickermax = $tickermax;\n\$redir = '$redir';\n\$captcha = " . ($captcha != '' ? 'true' : 'false') . ";\n\$forum = '$forum';\n\$bbcode1 = array(\"" . implode('", "', $bbcode1) . "\");\n\$bbcode2 = array('" . implode('\', \'', $bbcode2) . "');\n\$bbcode3 = array('" . implode('\', \'', $bbcode3) . "');\n?>"); #array_map('trim', " . ((substr($smilies, -4) != '.var') ? "array_slice(file('$smilies'), 1)" : "file('$smilies')") . ")
    fclose($temp);
}
if(file_exists('newsscript/cats.php') && (filemtime('newsscript/cats.php') > filemtime($newscatsdat)))
    include('newsscript/cats.php');
else
{
    //Kats: ID, Name, Bild
    $cats = array_map('trim', array_slice(file($newscatsdat), 1));
    $towrite = "<?php\n//Auto-generated config!\n\$cats = array();\n";
    foreach($cats as $value)
    {
        $value = explode("\t", $value);
        $towrite .= '$cats[' . $value[0] . '][] = \'' . $value[1] . "';\n";
        $towrite .= '$cats[' . $value[0] . '][] = \'' . (isset($value[2]) && $value[2] ? (strpos($value[2], '/') === false ? $newscatpics : null) . $value[2] : null) . "';\n";
    }
    $temp = fopen('newsscript/cats.php', 'w');
    fwrite($temp, $towrite . '?>');
    fclose($temp);
    unset($cats);
    include('newsscript/cats.php');
}
if(is_array($smilies))
    ; //Falls Smilies bereits durch den Newsticker gesetzt sind
elseif($smilies)
{
    if(file_exists('newsscript/smilies.php') && (filemtime('newsscript/smilies.php') > filemtime($smilies)))
        include('newsscript/smilies.php');
    else
    {
        //Smilies: ID, Synonym, Bild
        $smilies = array_map('trim', (substr($smilies, -4) != '.var' ? array_slice(file($smilies), 1) : array_map('utf8_encode', file($smilies))));
        $towrite = "<?php\n//Auto-generated config!\n\$smilies = array();\n";
        foreach($smilies as $value)
        {
            $value = explode("\t", $value);
            $towrite .= '$smilies[\'' . $value[1] . '\'] = \'<img src="' . $forum . (strpos($value[2], '/') === false ? $smiliepics : '') . $value[2] . '" alt="' . $value[1] . "\" style=\"border:none;\" />';\n";
        }
        $temp = fopen('newsscript/smilies.php', 'w');
        fwrite($temp, $towrite . '$htmlJSDecode = array_combine(array_keys($htmlJSDecode = array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))+array(\'&#039;\' => "\'")), array_map(function($string){return \'\u00\' . bin2hex($string);}, array_values($htmlJSDecode)));' . "\n?>");
        fclose($temp);
        unset($smilies);
        include('newsscript/smilies.php');
    }
}
else
    $smilies = array();

//$action laden
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
if(!isset($_SESSION))
    session_start();

//Mehr Smilies
if($action == 'smilies')
{
    include('newsscript/functions.php');
    newsHead('CHS - Newsscript: Mehr Smilies', 'Newsscript, CHS, Mehr Smilies, Chrissyx', 'Mehr Smilies des Newsscript von CHS');
    $i=0;
    foreach($smilies as $key => $value)
    {
        if((++$i % $smiliesmaxrow) == 0)
            echo("<br />\n");
        echo('  <a href="javascript:(n = opener.document.getElementById(\'newsbox\')).value += \' ' . strtr($key, $htmlJSDecode) . '\'; n.focus();">' . $value . "</a>\n");
    }
    newsTail();
    exit();
}

//CAPTCHA
elseif($action == 'captcha')
{
    for($i=0, $captcha=''; $i<5; $i++)
        $captcha .= chr(mt_rand(48, 90));
    $_SESSION['captcha'] = $captcha;
    $captcha = imagecreatetruecolor(40, 20);
    $red = imagecolorallocate($captcha, 255, 0, 0);
    imagestring($captcha, 3, 3, 3, $_SESSION['captcha'], $red);
    imagepng($captcha);
    imagedestroy($captcha);
    exit();
}

//TBB1 threading
elseif($action == 'threading' && $_SESSION['dispall'])
{
    $threads = @array_reverse(array_map('trim', file($forum . 'foren/' . $_GET['foren_id'] . '-threads.xbb'))) or die('<b>ERROR:</b> Forum nicht gefunden!');
    echo('<select size="' . count($threads) . '" onclick="opener.document.getElementById(\'newsbox\').value += \'\n[url=' . $forum . 'index.php?mode=viewthread&amp;forum_id=' . $_GET['foren_id'] . "&amp;thread=' + this.options[this.options.selectedIndex].value + ']Link zum Thema im Forum[/url]'; window.close();\">\n");
    foreach($threads as $thread)
    {
        $topic = file($forum . 'foren/' . $_GET['foren_id'] . '-' . $thread . '.xbb');
        $topic = explode("\t", $topic[0]);
        echo('<option value="' . $thread . '">' . $topic[1] . "</option>\n");
    }
    die('</select>');
}

//Admin Login
elseif($action == 'admin')
{
    include('newsscript/functions.php');
    include('newsscript/language_login.php');
    $_SESSION['dispall'] = false;
    $user = @array_map('trim', array_slice(file($newspwsdat), 1)) or die($lang['login']['nouser']);
    if(isset($_POST['name']) && ($key = unifyUser($user, $_POST['name'] = stripEscape($_POST['name']))) !== false) //Nutzer holen
    {
        unset($_POST['name']);
        $value = explode("\t", $user[$key]);
        $_SESSION['newsname'] = $value[0];
        if($value[2] >= ($_POST['edit'] == 'script')) //Rechte checken
        {
            if($_POST['edit'] == 'newpw') //Neues PW?
            {
                for($i=0,$newpw=''; $i<10; $i++)
                    $newpw .= chr(mt_rand(33, 126));
                $value[4] = md5($newpw);
                $user[$key] = implode("\t", $value);
                saveUser($newspwsdat, $user);
                if(!@mail($value[3], $_SERVER['SERVER_NAME'] . ' Newsscript: ' . $lang['login']['subject'], sprintf($lang['login']['mail'], $_SESSION['newsname'], $_SERVER['REMOTE_ADDR'], $newpw), 'From: newsscript@' . $_SERVER['SERVER_NAME'] . "\n" . 'Reply-To: ' . $value[3] . "\n" . 'X-Mailer: PHP/' . phpversion() . "\n" . 'Content-Type: text/plain; charset=' . $lang['login']['charset']))
                    $_POST['edit'] = 'nopw';
            }
            else //News oder Script
            {
                if($_POST['edit'] == 'script')
                    $redir = 'newsscript/index.php';
                else
                    $_SESSION['dispall'] = true;
                unset($_POST['edit']);
                $_SESSION['newspw'] = md5($_POST['newspw']);
                if(isset($value[4]) && $value[4] == $_SESSION['newspw']) //Neues PW checken
                {
                    $value[1] = $_SESSION['newspw'];
                    unset($value[4]);
                    $user[$key] = implode("\t", $value);
                    saveUser($newspwsdat, $user);
                }
                if($value[1] == $_SESSION['newspw']) //Passwort checken
                {
                    unset($_POST['newspw']);
                    $_SESSION['newsadmin'] = $value[2];
                    if($redir)
                    {
                        @header('Location: ' . $redir);
                        die($lang['login']['loggedin'] . ' <a href="' . $redir . '">' . $lang['login']['back'] . '</a>');
                    }
                }
                else
                    unset($_SESSION['newspw'], $_SESSION['dispall']);
            }
        }
    }
    if(!isset($_SESSION['newspw']))
    {
        newsHead('CHS - Newsscript: ' . $lang['login']['title'], 'Newsscript, CHS, ' . $lang['login']['title'] . ', Chrissyx', $lang['login']['title'] . ' des Newsscript von CHS', $lang['login']['charset'], $lang['login']['code'], null, 'newsscript/style.css');
?>
  <h3>CHS - Newsscript: <?php echo($lang['login']['title']); ?></h3>
  <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
  <table>
   <tr><td><?php echo($lang['login']['name']); ?></td><td><input type="text" name="name" value="<?php echo(isset($_POST['name']) && $_POST['name'] != '' ? $_POST['name'] : (isset($_SESSION['newsname']) ? $_SESSION['newsname'] : '')); ?>" <?php
        if(isset($_POST['name']))
            echo('style="border-color:#FF0000;" /></td></tr>
   <tr><td colspan="2"><span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['unknown'] . '</span><br ');?>/></td></tr>
   <tr><td><?php echo($lang['login']['pass']); ?></td><td><input type="password" name="newspw" <?php
        if(isset($_POST['newspw']) && !isset($_POST['edit']))
            echo('style="border-color:#FF0000;" /></td></tr>
   <tr><td colspan="2"><span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['wrongpass'] . '</span><br ');?>/></td></tr>
  </table>
  <input type="radio" name="edit" value="newpw" /><?php echo($lang['login']['reqpass']); ?>
<?php
        if(!isset($_POST['name']))
        {
            if(isset($_POST['edit']) && $_POST['edit'] == 'newpw')
                echo ('<br />
  <span class="green">&raquo; ' . $lang['login']['sendpass'] . '</span><br />');
            elseif(isset($_POST['edit']) && $_POST['edit'] == 'nopw')
                echo('<br />
  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['sendnopass'] . '</span><br />');
        }
?><br />
  <input type="radio" name="edit" value="news" checked="checked" /><?php echo($lang['login']['news']); ?><br />
  <input type="radio" name="edit" value="script" /><?php echo($lang['login']['script']); ?>
<?php
        if(isset($_POST['edit']) && $_POST['edit'] == 'script' && !isset($_POST['name']))
            echo('<br />
  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['norights'] . '</span><br />');
?><br />
  <input type="submit" value="<?php echo($lang['login']['login']); ?>" />
  <input type="hidden" name="action" value="admin" />
  </form>
<?php
        newsTail();
        exit();
    }
}

//Admin Logout
elseif($action == 'newsout')
    unset($_SESSION['newsname'], $_SESSION['newspw'], $_SESSION['newsadmin'], $_SESSION['dispall']);

//News lesen ----------------------------------------------------------------------------------------------------------------------------------------
include_once('newsscript/language_news.php');
echo('  <script type="text/javascript" src="https://static.addtoany.com/menu/page.js">var a2a_config = a2a_config || {};</script>' . "\n"); //Fire up AddToAny
//An attribute-based configuration for each AddToAny button is not possible due to validation problems
#<a class="a2a_dd" style="float:right;" data-a2a-url="' . ($redir ? $redir : 'http://' . str_replace('//', '/', $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/') . basename($_SERVER['PHP_SELF'])) . '?newsid=%s" data-a2a-title="%s"></a><br style="clear:left;" />
$newsTemplate = '  <div %1$s>
   <strong style="float:left; font-size:medium;">%2$s</strong>%3$s<br style="clear:left;" />
   <span style="font-size:small;">' . $lang['news']['postedby'] . ' <strong>%4$s</strong> &ndash; %5$s &ndash; %6$s ' . $lang['news']['oclock'] . ' &ndash; ' . $lang['news']['cat'] . ' <a href="' . ($redir ? $redir : $_SERVER['PHP_SELF']) . '%7$s">%8$s</a></span>
   <hr noshade="noshade" style="height:0; border-width:0 0 1px 0;" />
   %9$s
   <hr noshade="noshade" style="height:0; border-width:0 0 1px 0;" />
   %10$s<span style="float:left; font-size:small;">' . $lang['news']['sources'] . ' %11$s</span> <a class="a2a_dd" href="https://www.addtoany.com/share" style="float:right;"><img src="https://static.addtoany.com/buttons/share_save_171_16.png" style="width:171px; height:16px; border:none;" alt="Share"></a><br style="clear:left;" />
   <script type="text/javascript">a2a_config.linkname=\'%13$s\'; a2a_config.linkurl=\'' . ($redir ? $redir : 'http://' . str_replace('//', '/', $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/') . basename($_SERVER['PHP_SELF'])) . '?newsid=%12$s\'; a2a.init(\'page\');</script>
  </div><br />
';
$news = array_map('trim', file($newsdat)) or die($lang['news']['nonews']); #fgets()?

//Einzelnews
if(isset($_GET['newsid']))
{
    //Rechte checken
    if(in_array($action, array('delete', 'delcomment', 'edit')))
    {
        include('newsscript/functions.php');
        $user = @array_map('trim', array_slice(file($newspwsdat), 1)) or die($lang['news']['nouser']);
        if(($user = getUser($user, $_SESSION['newsname'])) == false)
            die($lang['news']['unknown']);
        elseif($user[1] != $_SESSION['newspw'])
            die($lang['news']['wrongpass']);
        elseif(!$_SESSION['dispall'])
            die($lang['news']['norights']);
    }

    foreach($news as $key => $value)
        if(current(sscanf($value, "%s")) != $_GET['newsid'])
            continue;
        else
        {
            $value = explode("\t", trim($news[$key]));
            if(!isset($value[8]))
                $value[8] = '';
//News löschen
            if($action == 'delete')
            {
                unset($news[$key]);
                saveNews($newsdat, $news);
                if(file_exists($newscomments . $_GET['newsid'] . '.dat'))
                    unlink($newscomments . $_GET['newsid'] . '.dat');
                echo('<div style="width:99%; text-align:center;"><p style="color:#008000; font-size:large;">' . $lang['news']['deletenews'] . "</p>\n<p><a href=\"" . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&amp;catid=' . $_GET['catid'] . '">' . $lang['news']['backtopage'] . "</a></p></div><br />\n");
            }
//News editieren
            elseif($action == 'edit')
            {
                if(!isset($_POST['headline'], $_POST['srcarray'], $_POST['newsbox'], $_POST['newsbox2']))
                {
                    list(, , , , $_POST['cat'], $_POST['headline'], $_POST['srcarray'], $_POST['newsbox'], $_POST['newsbox2']) = $value;
                    //Alles wieder dekodieren
                    $_POST['headline'] = htmlspecialchars_decode($_POST['headline'], ENT_QUOTES);
                    $_POST['srcarray'] = ($_POST['srcarray'] ? "\t" : '') . str_replace(array(' ', '&amp;'), array("\t", '&'), $_POST['srcarray']);
                    $_POST['newsbox'] = htmlspecialchars_decode(str_replace(array('<br />', '<br/>', '<br>'), "\n", $_POST['newsbox']), ENT_QUOTES);
                    $_POST['newsbox2'] = htmlspecialchars_decode(str_replace(array('<br />', '<br/>', '<br>'), "\n", $_POST['newsbox2']), ENT_QUOTES);
                    $_POST['preview'] = true;
                }
                $_POST['srcarray'] = explode("\t", $_POST['srcarray']);
                showJS($lang);
?>
<form name="newsform" id="newsform" action="<?php echo($_SERVER['PHP_SELF']); ?>?newsid=<?php echo($value[0]); ?>&amp;page=<?php echo($_GET['page']); ?>&amp;catid=<?php echo($_GET['catid']); ?>&amp;action=edit" method="post" onsubmit="addSource();">
<div style="background-color:#99CCFF; font-family:Arial,sans-serif; width:99%; border:1px solid #000000; padding:5px;">
 <h4>&raquo; <?php echo($lang['news']['editnews']); ?></h4>
<?php
                $temp = '';
//Editieren Vorschau
                if(isset($_POST['preview']) && $_POST['preview'])
                    echo(sprintf($newsTemplate,
                        'style="border:medium double #000000; padding:5px;"', //Style
                        preg_replace($bbcode1, $bbcode2, strtr(stripEscape($_POST['headline']), $smilies)), //Überschrift
                        !empty($_POST['cat']) && $cats[$_POST['cat']][1] ? ' <img src="' . $cats[$_POST['cat']][1] . '" alt="' . $cats[$_POST['cat']][0] . '" style="float:right; margin-left:5px;" />' : '', //Katbild
                        $value[3], //Autor
                        date($lang['news']['DATEFORMAT'], $value[1]), //Datum
                        date($lang['news']['TIMEFORMAT'], $value[1]), //Uhrzeit
                        '?catid=' . $value[4], //Katlink
                        !empty($_POST['cat']) ? $cats[$_POST['cat']][0] : '', //Kategorie
                        preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox']))), $smilies)), //News
                        $_POST['newsbox2'] ? preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox2']))), $smilies)) . "<hr noshade=\"noshade\" style=\"height:0; border-width:0 0 1px 0;\" />\n" : null, //Weiterlesen
                        (isset($_POST['srcarray'][1]) ? '<select style="width:100px; font-size:x-small;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;' . str_replace('&', '&amp;', implode('</option><option>', $_POST['srcarray'])) . '</option></select>' : $lang['news']['non']) . ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '">' . ($_POST['newsbox2'] ? $lang['news']['readon'] . ' / ' : '') . (file_exists($newscomments . $value[0] . '.dat') ? $lang['news']['comments'] . ' ( <strong>' . count(file($newscomments . $value[0] . '.dat')) . '</strong> )' : $lang['news']['writecomment']) . '</a>',
                        $value[0], //News ID
                        preg_replace($bbcode1, $bbcode3, stripEscape($_POST['headline'])) //Titel
                    ));
//Editieren posten
                elseif($_POST['update'])
                {
                    $temp = '  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['news']['fillout'] . '</span><br /><br />';
                    if($_POST['headline'] && $_POST['newsbox'])
                    {
                        #id - timestamp - ip - usrid?name? - catid - headline - quellen - text - weiterlesen
                        $news[$key] = $value[0] . "\t" . $value[1] . "\t" . $value[2] . "\t" . $value[3] . "\t" . (!empty($_POST['cat']) ? $_POST['cat'] : '') . "\t" . stripEscape($_POST['headline']) . "\t" . str_replace('&', '&amp;', implode(' ', array_slice($_POST['srcarray'], 1))) . "\t" . str_replace(array("\r", "\n"), '' , nl2br(stripEscape(trim($_POST['newsbox']) . "\t" . trim($_POST['newsbox2']))));
                        saveNews($newsdat, $news);
                        $temp = '   <span style="color:#008000; font-weight:bold;">&raquo; ' . $lang['news']['newsup'] . '</span> <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;catid=' . $_GET['catid'] . '">' . $lang['news']['back'] . '</a><br /><br />';
                    }
                }
//News bearbeiten
                echo($temp . $lang['news']['headline']);
?>
 <input type="text" name="headline" id="headline" value="<?php echo(stripEscape($_POST['headline'])); ?>" size="65" onclick="activeNewsbox = this.name;" /><br />
 <input type="button" value="B" style="font-weight:bold; width:25px;" onclick="setNewsTag('[b]', '[/b]');" />
 <input type="button" value="I" style="font-style:italic; width:25px;" onclick="setNewsTag('[i]', '[/i]');" />
 <input type="button" value="U" style="text-decoration:underline; width:25px;" onclick="setNewsTag('[u]', '[/u]');" />
 <input type="button" value="S" style="text-decoration:line-through; width:25px;" onclick="setNewsTag('[s]', '[/s]');" />
 <input type="button" value="<?php echo($lang['news']['center']); ?>" style="width:70px;" onclick="setNewsTag('[center]', '[/center]');" />
 <input type="button" value="<?php echo($lang['news']['quote']); ?>" style="width:63px;" onclick="setNewsTag('[quote]', '[/quote]');" />
 <input type="button" value="<?php echo($lang['news']['srccode']); ?>" style="font-family:monospace; position:relative; top:-0.1em; width:47px;" onclick="setNewsTag('[code]', '[/code]');" />
 <select style="width:95px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[color=' + this.options[this.options.selectedIndex].value + ']', '[/color]');">
  <option><?php echo($lang['news']['color']); ?></option>
  <option value="#000000" style="background-color:#000000; color:#000000;"><?php echo($lang['news']['black']); ?></option>
  <option value="#808080" style="background-color:#808080; color:#808080;"><?php echo($lang['news']['dark_grey']); ?></option>
  <option value="#800000" style="background-color:#800000; color:#800000;"><?php echo($lang['news']['dark_red']); ?></option>
  <option value="#FF0000" style="background-color:#FF0000; color:#FF0000;"><?php echo($lang['news']['red']); ?></option>
  <option value="#008000" style="background-color:#008000; color:#008000;"><?php echo($lang['news']['dark_green']); ?></option>
  <option value="#00FF00" style="background-color:#00FF00; color:#00FF00;"><?php echo($lang['news']['light_green']); ?></option>
  <option value="#808000" style="background-color:#808000; color:#808000;"><?php echo($lang['news']['ochre']); ?></option>
  <option value="#FFFF00" style="background-color:#FFFF00; color:#FFFF00;"><?php echo($lang['news']['yellow']); ?></option>
  <option value="#000080" style="background-color:#000080; color:#000080;"><?php echo($lang['news']['dark_blue']); ?></option>
  <option value="#0000FF" style="background-color:#0000FF; color:#0000FF;"><?php echo($lang['news']['blue']); ?></option>
  <option value="#800080" style="background-color:#800080; color:#800080;"><?php echo($lang['news']['dark_purple']); ?></option>
  <option value="#FF00FF" style="background-color:#FF00FF; color:#FF00FF;"><?php echo($lang['news']['purple']); ?></option>
  <option value="#008080" style="background-color:#008080; color:#008080;"><?php echo($lang['news']['dark_turquoise']); ?></option>
  <option value="#00FFFF" style="background-color:#00FFFF; color:#00FFFF;"><?php echo($lang['news']['turquoise']); ?></option>
  <option value="#C0C0C0" style="background-color:#C0C0C0; color:#C0C0C0;"><?php echo($lang['news']['grey']); ?></option>
  <option value="#FFFFFF" style="background-color:#FFFFFF; color:#FFFFFF;"><?php echo($lang['news']['white']); ?></option>
 </select>
 <select style="width:95px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[size=' + this.options[this.options.selectedIndex].value + ']', '[/size]');">
  <option><?php echo($lang['news']['size']); ?></option>
  <option value="-2"><?php echo($lang['news']['size_down2']); ?></option>
  <option value="-1"><?php echo($lang['news']['size_down1']); ?></option>
  <option value="+1"><?php echo($lang['news']['size_up1']); ?></option>
  <option value="+2"><?php echo($lang['news']['size_up2']); ?></option>
  <option value="+3"><?php echo($lang['news']['size_up3']); ?></option>
  <option value="+4"><?php echo($lang['news']['size_up4']); ?></option>
 </select><br />
 <input type="button" value="<?php echo($lang['news']['url']); ?>" style="width:60px;" onclick="setNewsTag('[url]', '[/url]');" />
 <input type="button" value="<?php echo($lang['news']['img']); ?>" style="width:60px;" onclick="setNewsTag('[img]', '[/img]');" />
 <input type="button" value="<?php echo($lang['news']['email']); ?>" style="width:70px;" onclick="setNewsTag('[email]', '[/email]');" />
 <input type="button" value="<?php echo($lang['news']['iframe']); ?>" style="width:70px;" onclick="setNewsTag('[iframe]', '[/iframe]');" />
 <button type="button" style="font-size:x-small; height:21px; width:50px;" onclick="setNewsTag('[sup]', '[/sup]');"><span style="position:relative; top:-0.3em;"><?php echo($lang['news']['superscript']); ?></span></button>
 <button type="button" style="font-size:x-small; height:21px; width:40px;" onclick="setNewsTag('[sub]', '[/sub]');"><span style="position:relative; bottom:-0.3em;"><?php echo($lang['news']['subscript']); ?></span></button>
 <input type="button" value="<?php echo($lang['news']['list']); ?>" style="width:65px;" onclick="setNewsTag('[list]\n[*]', '\n[/list]');" /><br />
 <select style="width:502px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[url=?newsid=' + this.options[this.options.selectedIndex].value + ']', '[/url]');">
  <option style="font-weight:bold;"><?php echo($lang['news']['linkoldnews']); ?></option>
<?php
                $size = ($size = count($news)) > 20 ? 21 : $size;
                for($i=1; $i<$size; $i++)
                {
                    $value = explode("\t", $news[$i]);
                    echo('  <option value="' . $value[0] . '">' . preg_replace($bbcode1, $bbcode3, $value[5]) . '</option>' . "\n");
                }
?>
 </select><br />
 <textarea name="newsbox" id="newsbox" rows="10" cols="60" style="margin-bottom:5px; float:left;" onclick="activeNewsbox = this.name;"><?php echo(stripEscape(trim($_POST['newsbox']))); ?></textarea>
<?php
                if($smilies)
                {
                    echo(' <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;"><strong>' . $lang['news']['smilies'] . '</strong><br />');
                    $i=0;
                    foreach($smilies as $key => $value)
                    {
                        if($i >= $smiliesmax)
                            break;
                        if(($i++ % $smiliesmaxrow) == 0)
                            echo("<br />\n");
                        echo('  <a href="javascript:setNewsTag(\' ' . $key . '\', \'\');">' . $value . '</a>');
                    }
                    echo('<br /><br />
  <input type="button" value="' . $lang['news']['moresmilies'] . '" onclick="window.open(\'news.php?action=smilies\', \'_blank\', \'width=250, resizable, scrollbars, status\');" />
 </div>');
                }
?><br style="clear:both;" />
 <?php echo($lang['news']['readontext']); ?> <input type="button" id="toggler" value="<?php echo(($_POST['newsbox2']) ? $lang['news']['discard'] . ' &l' : $lang['news']['expand'] . ' &r'); ?>aquo;" onclick="toggleFullStory();" /><br />
 <textarea name="newsbox2" id="newsbox2" rows="10" cols="60" style="margin-bottom:5px; display:<?php echo(($_POST['newsbox2']) ? 'inline' : 'none'); ?>;" onclick="activeNewsbox = this.name;"><?php echo(stripslashes(trim($_POST['newsbox2']))); ?></textarea><br />
 <?php echo($lang['news']['sources']); ?> <input type="text" name="sources" id="sources" size="25" /> <a href="javascript:doSource(true);"><?php echo($lang['news']['add']); ?></a> &ndash; <a href="javascript:doSource(false);"><?php echo($lang['news']['remove']); ?></a><br />
 <input type="submit" value="<?php echo($lang['news']['update']); ?>" /> <input type="submit" name="preview" value="<?php echo($lang['news']['preview']); ?>" style="font-weight:bold;" /> <?php echo($lang['news']['cat']); ?> <select name="cat" style="width:125px;">
<?php
                foreach($cats as $key => $value)
                    echo '  <option value="' . $key . '"' . ($key == $_POST['cat'] ? ' selected="selected"' : '') . '>' . $value[0] . '</option>';
?>
 </select> <input type="button" value="<?php echo($lang['news']['cancel']); ?>" onclick="document.location='<?php echo($_SERVER['PHP_SELF'].'?page='.$_GET['page'].'&amp;catid='.$_GET['catid']); ?>'" />
 <input type="hidden" name="update" value="true" />
 <input type="hidden" name="srcarray" id="srcarray" value="" />
</div>
</form>
<br />
<?php
            }
            else
            {
//Kommentar schreiben
                if($action == 'comment')
                {
                    $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['news']['fillout'] . '</span><br /><br />';
                    if(!($_POST['name'] = htmlspecialchars(stripslashes($_POST['name']), ENT_QUOTES)))
                        $_POST['name'] .= '" style="border-color:#FF0000;';
                    elseif($captcha && $_POST['captcha'] != $lang['news']['captcha_word'] /*$_SESSION['captcha']*/)
                        $_POST['captcha'] = 'border-color:#FF0000; ';
                    elseif($_POST['newsbox'])
                    {
                        #todo: still buggy regex?
                        $_POST['newsbox'] = preg_replace_callback("/([^ ^\n^\r^\]]+?:\/\/|www\.)[^ \[\.]+(\.[^ ^\n^\r\[\.]+)+/si", function($arr)
                        {
                            return ($arr[2]) ? '[url]' . (($arr[1] == 'www.') ? 'http://' : '') . $arr[0] . '[/url]' : $arr[0];
                        }, $_POST['newsbox']);
                        $temp = fopen($newscomments . $_GET['newsid'] . '.dat', 'a');
                        fwrite($temp, time() . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_POST['name'] . "\t" . str_replace(array("\r", "\n"), '' , nl2br(htmlspecialchars(stripslashes(trim($_POST['newsbox'])), ENT_QUOTES))) . "\n");
                        fclose($temp);
                        $_SESSION['shoutName'] = $_POST['name'];
                        unset($_POST['name'], $_POST['newsbox'], $_POST['captcha'], $_SESSION['captcha']);
                        $temp = '   <span style="color:#008000;">&raquo; ' . $lang['news']['thxcomment'] . '</span><br /><br />';
                    }
                }
//Kommentar löschen
                elseif($action == 'delcomment')
                {
                    $towrite = array_map('trim', file($newscomments . $_GET['newsid'] . '.dat')) or die($lang['news']['nocomment']);
                    unset($towrite[$_GET['id']]);
                    if(count($towrite) == 0)
                        unlink($newscomments . $_GET['newsid'] . '.dat');
                    else
                    {
                        $temp = fopen($newscomments . $_GET['newsid'] . '.dat', 'w');
                        fwrite($temp, implode("\n", $towrite) . "\n");
                        fclose($temp);
                    }
                    $temp = '   <span style="color:#008000;">&raquo; ' . $lang['news']['delcomment'] . '</span><br /><br />';
                }
                else
                    unset($temp);
?>

  <script type="text/javascript">
  function setNewsSmilie(smilie)
  {
   (n = document.getElementById('newsbox')).value += smilie;
   n.focus();
  }
  </script>

<?php
                echo(sprintf($newsTemplate,
                    'class="newsscriptmain" style="width:99%; border:1px solid #000000; padding:5px;"', //Style
                    preg_replace($bbcode1, $bbcode2, strtr($value[5], $smilies)), //Überschrift
                    !empty($value[4]) && $cats[$value[4]][1] ? '<img src="' . $cats[$value[4]][1] . '" alt="' . $cats[$value[4]][0] . '" style="margin-left:5px; float:right;" />' : '', //Katbild
                    $value[3], //Autor
                    date($lang['news']['DATEFORMAT'], $value[1]), //Datum
                    date($lang['news']['TIMEFORMAT'], $value[1]), //Uhrzeit
                    '?catid=' . $value[4], //Katlink
                    !empty($value[4]) ? $cats[$value[4]][0] : '', //Kategorie
                    preg_replace($bbcode1, $bbcode2, strtr($value[7], $smilies)), //News
                    isset($value[8]) && $value[8] != '' ? preg_replace($bbcode1, $bbcode2, strtr($value[8], $smilies)) . '<hr noshade="noshade" style="height:0; border-width:0 0 1px 0;" />' . "\n" : null, //Weiterlesen
                    (isset($value[6][1]) && $value[6][1] ? ' <select style="width:100px; font-size:x-small;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;</option><option>' . str_replace(' ', '</option><option>', $value[6]) . '</option></select>' : $lang['news']['non']) . (isset($_SESSION['dispall']) && $_SESSION['dispall'] ? ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;catid=' . (isset($_GET['catid']) ? $_GET['catid'] : '') . '&amp;action=edit">' . $lang['news']['edit'] . '</a> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;catid=' . (isset($_GET['catid']) ? $_GET['catid'] : '') . '&amp;action=delete" onclick="return confirm(\'' . $lang['news']['confirm'] . '\');">' . $lang['news']['delete'] . '</a>' : ''),
                    $value[0], //News ID
                    preg_replace($bbcode1, $bbcode3, $value[5]) //Titel
                ));
?>

  <p><a href="<?php echo($redir ? $redir : $_SERVER['PHP_SELF']); ?>?page=<?php echo($_GET['page']); ?>&amp;catid=<?php if(isset($_GET['catid'])) echo($_GET['catid']); ?>">&laquo; <?php echo($lang['news']['backtoall']); ?></a></p>
  <div class="newsscriptcomments" style="width:99%; border:1px solid #000000; padding:5px;">
   <h4>&raquo; <?php echo($lang['news']['comments']); ?></h4>
<?php
//Kommentare auslesen
                if(!file_exists($newscomments . $_GET['newsid'] . '.dat'))
                    echo('   <p>' . $lang['news']['noyet'] ."</p>\n");
                else
                    foreach(file($newscomments . $_GET['newsid'] . '.dat') as $key => $value)
                    {
                        $value = explode("\t", $value);
                        echo('<span style="font-style:italic;"><strong>' . $value[2] . '</strong> ' . $lang['news']['onday'] . ' <strong>' . date($lang['news']['DATEFORMAT'], $value[0]) . '</strong> ' . $lang['news']['atclock'] . ' <strong>' . date($lang['news']['TIMEFORMAT'], $value[0]) . "</strong>:</span><br />\n" . preg_replace($bbcode1, $bbcode2, strtr($value[3], $smilies)) . ((isset($_SESSION['dispall']) && $_SESSION['dispall']) ? "<br />\nIP: <strong>" . $value[1] . '</strong> &ndash; [ <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $_GET['newsid'] . '&amp;page=' . $_GET['page'] . '&amp;action=delcomment&amp;id=' . $key . '#box">' . $lang['news']['delete'] . '</a> ]' : '') . '<hr noshade="noshade" style="height:0; border-width:0 0 1px 0;" />' . "\n");
                    }
                echo((isset($temp) ? $temp : '') . "\n");
?>
   <form action="<?php echo($_SERVER['PHP_SELF']); ?>?newsid=<?php echo($_GET['newsid']); ?>&amp;page=<?php echo($_GET['page']); ?>&amp;catid=<?php if(isset($_GET['catid'])) echo($_GET['catid']); ?>&amp;action=comment#box" method="post">
   <div id="box" style="float:left;">
    <?php echo($lang['news']['name']); ?> <input type="text" name="name" value="<?php echo(!isset($_POST['name']) ? (!isset($_SERVER['newsname']) ? (isset($_SESSION['shoutName']) ? $_SESSION['shoutName'] : '') : $_SERVER['newsname']) : $_POST['name']); ?>" size="30" /><br />
    <textarea name="newsbox" id="newsbox" rows="5" cols="30"><?php if(isset($_POST['newsbox'])) echo(htmlspecialchars(stripslashes(trim($_POST['newsbox'])), ENT_QUOTES)); ?></textarea><br />
<?php echo($captcha ? '    <input type="text" name="captcha" style="' . (isset($_POST['captcha']) ? $_POST['captcha'] : '') . 'vertical-align:middle; width:110px;" /> &larr; ' . sprintf($lang['news']['captcha_text'], $lang['news']['captcha_word']) . '<img src="news.php?action=captcha" alt="CAPTCHA" style="display:none; vertical-align:middle;" /><br />' . "\n" : null); ?>
    <input type="submit" value="<?php echo($lang['news']['docomment']); ?>" style="font-weight:bold;" /> <input type="reset" value="<?php echo($lang['news']['reset']); ?>" />
   </div>
<?php
                if($smilies)
                {
                    echo('   <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;">
    <strong>' . $lang['news']['smilies'] . '</strong><br />');
                    $i=0;
                    foreach($smilies as $key => $value)
                    {
                        if($i >= $smiliesmax)
                            break;
                        if(($i++ % $smiliesmaxrow) == 0)
                            echo("<br />\n");
                        echo('    <a href="javascript:setNewsSmilie(\' ' . strtr($key, $htmlJSDecode) . '\');">' . $value . '</a>');
                    }
                    echo('<br /><br />
    <input type="button" value="' . $lang['news']['moresmilies'] . '" onclick="window.open(\'news.php?action=smilies\', \'_blank\', \'width=250, resizable, scrollbars, status\');" />
   </div>');
                }
?><br style="clear:left;" />
   </form>
  </div><br />
<?php
            }
        }
        #todo: notfound
}
else
{
//News
    if(isset($_SESSION['dispall']) && $_SESSION['dispall'] === true) //Formular zeigen
    {
        include_once('newsscript/functions.php');
        $user = @array_map('trim', array_slice(file($newspwsdat), 1)) or die($lang['news']['nouser']);
        if(($user = getUser($user, $_SESSION['newsname'])) == false)
            die($lang['news']['unknown']);
        elseif($user[1] != $_SESSION['newspw'])
            die($lang['news']['wrongpass']);
        else
            $_POST['srcarray'] = isset($_POST['srcarray']) ? explode("\t", $_POST['srcarray']) : array('');
        showJS($lang);
?>
<form name="newsform" id="newsform" action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="addSource();">
<div style="background-color:#99CCFF; font-family:Arial,sans-serif; width:99%; border:1px solid #000000; padding:5px;">
 <h4>&raquo; <?php echo($lang['news']['addnews']); ?></h4>
<?php
        $temp = '';
//News Vorschau
        if(isset($_POST['preview']))
            echo(sprintf($newsTemplate,
                'style="border:medium double #000000; padding:5px;"', //Style
                preg_replace($bbcode1, $bbcode2, strtr(stripEscape($_POST['headline']), $smilies)), //Überschrift
                isset($_POST['cat']) && $cats[$_POST['cat']][1] ? ' <img src="' . $cats[$_POST['cat']][1] . '" alt="' . $cats[$_POST['cat']][0] . '" style="margin-left:5px; float:right;" />' : '', //Katbild
                $_SESSION['newsname'], //Autor
                date($lang['news']['DATEFORMAT']), //Datum
                date($lang['news']['TIMEFORMAT']), //Uhrzeit
                '?catid=' . (isset($_POST['cat']) ? $_POST['cat'] : ''), //Katlink
                isset($_POST['cat']) ? $cats[$_POST['cat']][0] : '', //Kategorie
                preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox']))), $smilies)), //News
                $_POST['newsbox2'] ? preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox2']))), $smilies)) . '<hr noshade="noshade" style="height:0; border-width:0 0 1px 0;" />' . "\n" : null, //Weiterlesen
                (isset($_POST['srcarray'][1]) ? '<select style="width:100px; font-size:x-small;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;' . str_replace('&', '&amp;', implode('</option><option>', $_POST['srcarray']/*array_map('substr', $_POST['srcarray'], array_fill(0, $size, 0), array_fill(0, $size, 20))*/)) . '</option></select>' : $lang['news']['non']) . ' &ndash; <a href="#">' . ($_POST['newsbox2'] ? $lang['news']['readon'] . ' / ' : '') . $lang['news']['writecomment'] . '</a>',
                $news[0], //News ID
                preg_replace($bbcode1, $bbcode3, stripEscape($_POST['headline'])) //Titel
            ));
//News posten
        elseif(isset($_POST['update']))
        {
            $temp = '  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['news']['fillout'] . '</span><br /><br />';
            if($_POST['headline'] && $_POST['newsbox'])
            {
                array_unshift($news, ++$news[0]);
                #id - timestamp - ip - usrid?name? - catid - headline - quellen - text - weiterlesen
                $news[1] = /*(@current(sscanf($news[0], "%d\t[.*]"))+1)*/ ($news[0]-1) . "\t" . time() . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SESSION['newsname'] . "\t" . (isset($_POST['cat']) ? $_POST['cat'] : '') . "\t" . stripEscape($_POST['headline']) . "\t" . str_replace('&', '&amp;', implode(' ', array_slice($_POST['srcarray'], 1))) . "\t" . str_replace(array("\r", "\n"), '' , nl2br(stripEscape(trim($_POST['newsbox']) . "\t" . trim($_POST['newsbox2']))));
                saveNews($newsdat, $news);
                unset($_POST['cat'], $_POST['headline'], $_POST['srcarray'], $_POST['newsbox'], $_POST['newsbox2']);
                $temp = '   <span style="color:#008000; font-weight:bold;">&raquo; ' . $lang['news']['newspost'] . '</span><br /><br />';
            }
        }
//News erstellen
        echo($temp . $lang['news']['headline']);
?> <input type="text" name="headline" id="headline" value="<?php if(isset($_POST['headline'])) echo(stripEscape($_POST['headline'])); ?>" size="65" onclick="activeNewsbox = this.name;" /><br />
 <input type="button" value="B" style="font-weight:bold; width:25px;" onclick="setNewsTag('[b]', '[/b]');" />
 <input type="button" value="I" style="font-style:italic; width:25px;" onclick="setNewsTag('[i]', '[/i]');" />
 <input type="button" value="U" style="text-decoration:underline; width:25px;" onclick="setNewsTag('[u]', '[/u]');" />
 <input type="button" value="S" style="text-decoration:line-through; width:25px;" onclick="setNewsTag('[s]', '[/s]');" />
 <input type="button" value="<?php echo($lang['news']['center']); ?>" style="width:70px;" onclick="setNewsTag('[center]', '[/center]');" />
 <input type="button" value="<?php echo($lang['news']['quote']); ?>" style="width:63px;" onclick="setNewsTag('[quote]', '[/quote]');" />
 <input type="button" value="<?php echo($lang['news']['srccode']); ?>" style="font-family:monospace; position:relative; top:-0.1em; width:47px;" onclick="setNewsTag('[code]', '[/code]');" />
 <select style="width:95px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[color=' + this.options[this.options.selectedIndex].value + ']', '[/color]');">
  <option><?php echo($lang['news']['color']); ?></option>
  <option value="#000000" style="background-color:#000000; color:#000000;"><?php echo($lang['news']['black']); ?></option>
  <option value="#808080" style="background-color:#808080; color:#808080;"><?php echo($lang['news']['dark_grey']); ?></option>
  <option value="#800000" style="background-color:#800000; color:#800000;"><?php echo($lang['news']['dark_red']); ?></option>
  <option value="#FF0000" style="background-color:#FF0000; color:#FF0000;"><?php echo($lang['news']['red']); ?></option>
  <option value="#008000" style="background-color:#008000; color:#008000;"><?php echo($lang['news']['dark_green']); ?></option>
  <option value="#00FF00" style="background-color:#00FF00; color:#00FF00;"><?php echo($lang['news']['light_green']); ?></option>
  <option value="#808000" style="background-color:#808000; color:#808000;"><?php echo($lang['news']['ochre']); ?></option>
  <option value="#FFFF00" style="background-color:#FFFF00; color:#FFFF00;"><?php echo($lang['news']['yellow']); ?></option>
  <option value="#000080" style="background-color:#000080; color:#000080;"><?php echo($lang['news']['dark_blue']); ?></option>
  <option value="#0000FF" style="background-color:#0000FF; color:#0000FF;"><?php echo($lang['news']['blue']); ?></option>
  <option value="#800080" style="background-color:#800080; color:#800080;"><?php echo($lang['news']['dark_purple']); ?></option>
  <option value="#FF00FF" style="background-color:#FF00FF; color:#FF00FF;"><?php echo($lang['news']['purple']); ?></option>
  <option value="#008080" style="background-color:#008080; color:#008080;"><?php echo($lang['news']['dark_turquoise']); ?></option>
  <option value="#00FFFF" style="background-color:#00FFFF; color:#00FFFF;"><?php echo($lang['news']['turquoise']); ?></option>
  <option value="#C0C0C0" style="background-color:#C0C0C0; color:#C0C0C0;"><?php echo($lang['news']['grey']); ?></option>
  <option value="#FFFFFF" style="background-color:#FFFFFF; color:#FFFFFF;"><?php echo($lang['news']['white']); ?></option>
 </select>
 <select style="width:95px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[size=' + this.options[this.options.selectedIndex].value + ']', '[/size]');">
  <option><?php echo($lang['news']['size']); ?></option>
  <option value="-2"><?php echo($lang['news']['size_down2']); ?></option>
  <option value="-1"><?php echo($lang['news']['size_down1']); ?></option>
  <option value="+1"><?php echo($lang['news']['size_up1']); ?></option>
  <option value="+2"><?php echo($lang['news']['size_up2']); ?></option>
  <option value="+3"><?php echo($lang['news']['size_up3']); ?></option>
  <option value="+4"><?php echo($lang['news']['size_up4']); ?></option>
 </select><br />
 <input type="button" value="<?php echo($lang['news']['url']); ?>" style="width:60px;" onclick="setNewsTag('[url]', '[/url]');" />
 <input type="button" value="<?php echo($lang['news']['img']); ?>" style="width:60px;" onclick="setNewsTag('[img]', '[/img]');" />
 <input type="button" value="<?php echo($lang['news']['email']); ?>" style="width:70px;" onclick="setNewsTag('[email]', '[/email]');" />
 <input type="button" value="<?php echo($lang['news']['iframe']); ?>" style="width:70px;" onclick="setNewsTag('[iframe]', '[/iframe]');" />
 <button type="button" style="font-size:x-small; height:21px; width:50px;" onclick="setNewsTag('[sup]', '[/sup]');"><span style="position:relative; top:-0.3em;"><?php echo($lang['news']['superscript']); ?></span></button>
 <button type="button" style="font-size:x-small; height:21px; width:40px;" onclick="setNewsTag('[sub]', '[/sub]');"><span style="position:relative; bottom:-0.3em;"><?php echo($lang['news']['subscript']); ?></span></button>
 <input type="button" value="<?php echo($lang['news']['list']); ?>" style="width:65px;" onclick="setNewsTag('[list]\n[*]', '\n[/list]');" /><br />
 <select style="width:502px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[url=?newsid=' + this.options[this.options.selectedIndex].value + ']', '[/url]');">
  <option style="font-weight:bold;"><?php echo($lang['news']['linkoldnews']); ?></option>
<?php
        $size = ($size = count($news)) > 20 ? 21 : $size;
        for($i=1; $i<$size; $i++)
        {
            $value = explode("\t", $news[$i]);
            echo('  <option value="' . $value[0] . '">' . preg_replace($bbcode1, $bbcode3, $value[5]) . '</option>' . "\n");
        }
?>
 </select>
 <!--select onchange="if(this.options[this.options.selectedIndex].value != '') window.open('news.php?action=threading&amp;foren_id=' + this.options[this.options.selectedIndex].value, 'Threading', 'scrollbars, resizable');">
  <option value="">Link zum Thema ins TBB1...</option>
<?php
/*
        $foren = file($forum . 'vars/foren.var');
        $kg = file($forum . 'vars/kg.var');
        $size = count($kg);
        for($i=0; $i<$size; $i++)
        {
            $ak_kg = explode("\t", $kg[$i]);
            echo('  <option value="" style="background-color:#333333; color:#FFFFFF;">--' . $ak_kg[1] . "</option>\n");
            foreach($foren as $ak_forum)
            {
                $ak_forum = explode("\t", $ak_forum);
                if($ak_forum[5] == $ak_kg[0])
                    echo('  <option value="' . $ak_forum[0] . '">' . $ak_forum[1] . "</option>\n");
            }
            echo("  <option value=\"\"></option>\n");
        }
*/
?>
 </select--><br />
 <textarea name="newsbox" id="newsbox" rows="10" cols="60" style="margin-bottom:5px; float:left;" onclick="activeNewsbox = this.name;"><?php if(isset($_POST['newsbox'])) echo(stripEscape(trim($_POST['newsbox']))); ?></textarea>
<?php
        if($smilies)
        {
            echo(' <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;"><strong>' . $lang['news']['smilies'] . '</strong><br />');
            $i=0;
            foreach($smilies as $key => $value)
            {
                if($i >= $smiliesmax)
                    break;
                if(($i++ % $smiliesmaxrow) == 0)
                    echo("<br />\n");
                echo('  <a href="javascript:setNewsTag(\' ' . strtr($key, $htmlJSDecode) . '\', \'\');">' . $value . '</a>');
            }
            echo('<br /><br />
  <input type="button" value="' . $lang['news']['moresmilies'] . '" onclick="window.open(\'news.php?action=smilies\', \'_blank\', \'width=250, resizable, scrollbars, status\');" />
 </div>');
        }
?><br style="clear:both;" />
 <?php echo($lang['news']['readontext']); ?> <input type="button" id="toggler" value="<?php echo(isset($_POST['newsbox2']) && !empty($_POST['newsbox2']) ? $lang['news']['discard'] . ' &l' : $lang['news']['expand'] . ' &r'); ?>aquo;" onclick="toggleFullStory();" /><br />
 <textarea name="newsbox2" id="newsbox2" rows="10" cols="60" style="margin-bottom:5px; display:<?php echo(isset($_POST['newsbox2']) && !empty($_POST['newsbox2']) ? 'inline' : 'none'); ?>;" onclick="activeNewsbox = this.name;"><?php if(isset($_POST['newsbox2'])) echo(stripslashes(trim($_POST['newsbox2']))); ?></textarea><br />
 <?php echo($lang['news']['sources']); ?> <input type="text" name="sources" id="sources" size="25" /> <a href="javascript:doSource(true);"><?php echo($lang['news']['add']); ?></a> &ndash; <a href="javascript:doSource(false);"><?php echo($lang['news']['remove']); ?></a><br />
 <input type="submit" value="<?php echo($lang['news']['postnews']); ?>" /> <input type="submit" name="preview" value="<?php echo($lang['news']['preview']); ?>" style="font-weight:bold;" /> <!--<input type="reset" value="<?php echo($lang['news']['reset']); ?>" />--> <?php echo($lang['news']['cat']); ?> <select name="cat" style="width:125px;">
<?php
        foreach($cats as $key => $value)
            echo('  <option value="' . $key . '"' . ($key == $_POST['cat'] ? ' selected="selected"' : '') . '>' . $value[0] . '</option>');
?>
 </select> <input type="button" value="<?php echo($lang['news']['logout']); ?>" onclick="document.location='<?php echo($_SERVER['PHP_SELF']); ?>?action=newsout'" />
 <input type="hidden" name="update" value="true" />
 <input type="hidden" name="srcarray" id="srcarray" value="" />
</div>
</form>
<br />
<?php
    }

    if(!isset($news[1]))
        echo('<div class="newsscriptmain" style="width:99%; text-align:center;">' . $lang['news']['nofound'] . "</div><br />\n");
    else
    {
//News zeigen
        $size = count($news = ($_GET['catid'] = isset($_GET['catid']) ? $_GET['catid'] : '') ? array_values(array_filter($news, function($cur)
        {
            return strpos($cur, "\t" . intval($_GET['catid']) . "\t", 1) > 0 ? true : false;
        })) : array_slice($news, 1));
        $_GET['page'] = !isset($_GET['page']) || !is_numeric($_GET['page']) ? '' : ($_GET['page'] < 0 ? 0 : (($_GET['page']*$newsmax >= $size) ? abs($_GET['page']-1) : $_GET['page']));
        $start = empty($_GET['page']) ? 0 : $_GET['page']*$newsmax;
        $end = (($size-$start) > $newsmax) ? $start+$newsmax : $size;
        for($i=$start; $i<$end; $i++)
        {
            $value = explode("\t", $news[$i]);
            echo(sprintf($newsTemplate,
                'class="newsscriptmain" style="width:99%; border:1px solid #000000; padding:5px;"', //Style
                preg_replace($bbcode1, $bbcode2, strtr($value[5], $smilies)), //Überschrift
                !empty($value[4]) && $cats[$value[4]][1] ? '<img src="' . $cats[$value[4]][1] . '" alt="' . $cats[$value[4]][0] . '" style="float:right; margin-left:5px;" />' : '', //Katbild
                $value[3], //Autor
                date($lang['news']['DATEFORMAT'], $value[1]), //Datum
                date($lang['news']['TIMEFORMAT'], $value[1]), //Uhrzeit
                '?catid=' . $value[4], //Katlink
                !empty($value[4]) ? $cats[$value[4]][0] : '', //Kategorie
                preg_replace($bbcode1, $bbcode2, strtr($value[7], $smilies)), //News
                null, //Weiterlesen
                ($value[6] ? '<select style="font-size:x-small; width:100px;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;</option><option>' . str_replace(' ', '</option><option>', $value[6]) . '</option></select>' : $lang['news']['non']) . ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;catid=' . $_GET['catid'] . '">' . (!empty($value[8]) ? $lang['news']['readon'] . ' / ' : '') . (file_exists($newscomments . $value[0] . '.dat') ? $lang['news']['comments'] . ' ( <strong>' . count(file($newscomments . $value[0] . '.dat')) . '</strong> )' : $lang['news']['writecomment']) . '</a>' . (isset($_SESSION['dispall']) && $_SESSION['dispall'] === true ? ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;catid=' . $_GET['catid'] . '&amp;action=edit">' . $lang['news']['edit'] . '</a> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;catid=' . $_GET['catid'] . '&amp;action=delete" onclick="return confirm(\'' . $lang['news']['confirm'] . '\');">' . $lang['news']['delete'] . '</a>' : ''),
                $value[0], //News ID
                preg_replace($bbcode1, $bbcode3, $value[5]) //Titel
            ));
        }
        echo('  <div class="newsscriptfooter" style="width:99%; text-align:center; font-size:small;">
  <a href="' . $_SERVER['PHP_SELF'] . '?page=0&amp;catid=' . $_GET['catid'] . '">&laquo;</a> <a href="' . $_SERVER['PHP_SELF'] . '?page=' . (intval($_GET['page'])-1) . '&amp;catid=' . $_GET['catid'] . '">&lsaquo; ' . $lang['news']['prev'] . '</a> &ndash; <select onchange="document.location=\'' . $_SERVER['PHP_SELF'] . '?page=\' + this.options[this.options.selectedIndex].value + \'&amp;catid=' . $_GET['catid'] . '\';" style="font-size:x-small; vertical-align:middle;">
');
        for($i=0; $i<ceil($size/$newsmax); $i++)
            echo('   <option value="' . $i . '"' . ($i == $_GET['page'] ? ' selected="selected"' : '') . '>' . $lang['news']['page'] . ' ' . ($i+1) . "</option>\n");
        echo('  </select> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . (intval($_GET['page'])+1) . '&amp;catid=' . $_GET['catid'] . '">' . $lang['news']['next'] . ' &rsaquo;</a> <a href="' . $_SERVER['PHP_SELF'] . '?page=' . floor($size/$newsmax) . '&amp;catid=' . $_GET['catid'] . '">&raquo;</a><br />
  ' . ($_GET['catid'] ? sprintf($lang['news']['showingpart'], ($start+1), (($end > $size) ? $size : $end), $size, $cats[$_GET['catid']][0], '<a href="' . $_SERVER['PHP_SELF'] . '">') . '</a>' : sprintf($lang['news']['showing'], ($start+1), (($end > $size) ? $size : $end), $size)) . "\n </div><br />\n ");
    }
}
#PLEASE DON'T REMOVE THIS!
?><div style="width:99%; text-align:center; font-size:xx-small;">Powered by CHS - Newsscript<br />&copy; 2008&ndash;2023 by Chrissyx</div>