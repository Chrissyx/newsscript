<?php
/**
 * Newsmodul zum Anzeigen und Verwalten der News. Verarbeitet auch Login und Passwörter.
 * 
 * @author Chrissyx
 * @copyright (c) 2001 - 2009 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Newsscript
 * @version 1.0.2
 */
#todo: <hr noshade="noshade" style="height:0; border-width:0 0 1px 0;" />

//Caching
if(file_exists('newsscript/settings.php') && (filemtime('newsscript/settings.php') > filemtime('newsscript/settings.dat.php'))) include_once('newsscript/settings.php');
else
{
 //Config: News, Anzahl, Passwörter, Kommntare, Kategorien, Bilder Ordner, Smilies, Smilie Ordner, Smilies Anzahl, Smilies Anzahl Reihe, Newsticker Anzahl, Redir nach Login
 list($newsdat, $newsmax, $newspwsdat, $newscomments, $newscatsdat, $newscatpics, $smilies, $smiliepics, $smiliesmax, $smiliesmaxrow, $tickermax, $redir, $captcha) = @array_map('trim', array_slice(explode("\n", file_get_contents('newsscript/settings.dat.php')), 1)) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
 if(($forum = substr($smilies, -4) == '.var' ? implode('/', array_slice(explode('/', $smilies), 0, -2)) : '') != '') $forum .= '/';
 $bbcode1 = array("/\[b\](.*?)\[\/b\]/si",
                  "/\[i\](.*?)\[\/i\]/si",
                  "/\[u\](.*?)\[\/u\]/si",
                  "/\[s\](.*?)\[\/s\]/si",
                  "/\[center\](.*?)\[\/center\]/si",
                  "/\[email\](.*?)\[\/email\]/si",
                  "/\[img\](.*?)\[\/img\]/si",
                  "/\[img=(.*?)\](.*?)\[\/img\]/si",
                  "/\[url\](.*?)\[\/url\]/si",
                  "/\[url=(.*?)\](.*?)\[\/url\]/si",
                  "/\[color=(\#[a-fA-F0-9]{6}|[a-zA-Z]+)\](.*?)\[\/color\]/si",
                  "/\[code\](.*?)\[\/code\]/si",
                  "/\[quote\](.*?)\[\/quote\]/si",
                  "/\[flash\](.*?)\[\/flash\]/si",
                  "/\[flash=(\d+),(\d+)\](.*?)\[\/flash\]/si");
 $bbcode2 = array('<span style="font-weight:bold;">\1</span>',
                  '<span style="font-style:italic;">\1</span>',
                  '<span style="text-decoration:underline;">\1</span>',
                  '<span style="text-decoration:line-through;">\1</span>',
                  '<p style="text-align:center;">\1</p>',
                  '<a href="mailto:\1">\1</a>',
                  '<img src="\1" alt="" />',
                  '<img src="\1" alt="\2" title="\2" />',
                  '<a href="\1" target="_blank">\1</a>',
                  '<a href="\1" target="_blank">\2</a>',
                  '<span style="color:\1;">\2</span>',
                  '<code>\1</code>',
                  '<blockquote><p style="font-style:italic;">\1</p></blockquote>',
                  '<object data="\1" type="application/x-shockwave-flash" width="425" height="355">
 <param name="allowscriptaccess" value="samedomain" />
 <param name="movie" value="\1" />
 <param name="quality" value="autohigh" />
 <param name="wmode" value="transparent" />
 <p>No flash installed! Please update your browser</p>
</object>',
                  '<object data="\3" type="application/x-shockwave-flash" width="\1" height="\2">
 <param name="allowscriptaccess" value="samedomain" />
 <param name="movie" value="\3" />
 <param name="quality" value="autohigh" />
 <param name="wmode" value="transparent" />
 <p>No flash installed! Please update your browser</p>
</object>');
 $temp = fopen('newsscript/settings.php', 'w');
 fwrite($temp, "<?php\n//Auto-generated config!\n\$newsdat = '$newsdat';\n\$newsmax = $newsmax;\n\$newspwsdat = '$newspwsdat';\n\$newscomments = '$newscomments';\n\$newscatsdat = '$newscatsdat';\n\$newscatpics = '$newscatpics';\n\$smilies = '$smilies';\n\$smiliepics = '$smiliepics';\n\$smiliesmax = " . ($smiliesmax ? $smiliesmax : "''") . ";\n\$smiliesmaxrow = " . ($smiliesmaxrow ? $smiliesmaxrow : "''") . ";\n\$tickermax = $tickermax;\n\$redir = '$redir';\n\$captcha = " . ($captcha != '' ? 'true' : 'false') . ";\n\$forum = '$forum';\n\$bbcode1 = array(\"" . implode('", "', $bbcode1) . "\");\n\$bbcode2 = array('" . implode('\', \'', $bbcode2) . "');\n?>"); #array_map('trim', " . ((substr($smilies, -4) != '.var') ? "array_slice(file('$smilies'), 1)" : "file('$smilies')") . ")
 fclose($temp);
}
if(file_exists('newsscript/cats.php') && (filemtime('newsscript/cats.php') > filemtime($newscatsdat))) include('newsscript/cats.php');
else
{
 //Kats: ID, Name, Bild
 $cats = array_map('trim', array_slice(file($newscatsdat), 1));
 $towrite = "<?php\n//Auto-generated config!\n\$cats = array();\n";
 foreach($cats as $value)
 {
  $value = explode("\t", $value);
  $towrite .= '$cats[' . $value[0] . '][] = \'' . $value[1] . "';\n";
  $towrite .= '$cats[' . $value[0] . '][] = \'' . (isset($value[2]) && strpos($value[2], '/') === false && $value[2] ? $newscatpics . $value[2] : null) . "';\n";
 }
 $temp = fopen('newsscript/cats.php', 'w');
 fwrite($temp, $towrite . '?>');
 fclose($temp);
 unset($cats);
 include('newsscript/cats.php');
}
if(is_array($smilies)); //Falls Smilies bereits durch den Newsticker gesetzt sind
elseif($smilies)
{
 if(file_exists('newsscript/smilies.php') && (filemtime('newsscript/smilies.php') > filemtime($smilies))) include('newsscript/smilies.php');
 else
 {
  //Smilies: ID, Synonym, Bild
  $smilies = array_map('trim', (substr($smilies, -4) != '.var' ? array_slice(file($smilies), 1) : file($smilies)));
  $towrite = "<?php\n//Auto-generated config!\n\$smilies = array();\n";
  foreach($smilies as $value)
  {
   $value = explode("\t", $value);
   $towrite .= '$smilies[\'' . $value[1] . '\'] = \'<img src="' . $forum . (strpos($value[2], '/') === false ? $smiliepics : '') . $value[2] . '" alt="' . $value[1] . "\" style=\"border:none;\" />';\n";
  }
  $temp = fopen('newsscript/smilies.php', 'w');
  fwrite($temp, $towrite . '$htmlJSDecode = array_combine(array_keys($htmlJSDecode = array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))+array(\'&#039;\' => "\'")), array_map(create_function(\'$string\', \'return \\\'\u00\\\' . bin2hex($string);\'), array_values($htmlJSDecode)));' . "\n?>");
  fclose($temp);
  unset($smilies);
  include('newsscript/smilies.php');
 }
}
else $smilies = array();

//$action laden
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
session_start();

//Mehr Smilies
if($action == 'smilies')
{
 include('newsscript/functions.php');
 newsHead('CHS - Newsscript: Mehr Smilies', 'Newsscript, CHS, Mehr Smilies, Chrissyx', 'Mehr Smilies des Newsscript von CHS');
 $i=0;
 foreach($smilies as $key => $value)
 {
  if((++$i % $smiliesmaxrow) == 0) echo("<br />\n");
  echo('  <a href="javascript:opener.document.getElementById(\'newsbox\').value += \' ' . strtr($key, $htmlJSDecode) . '\'; opener.document.getElementById(\'newsbox\').focus();">' . $value . "</a>\n");
 }
 newsTail();
 exit();
}

//CAPTCHA
elseif($action == 'captcha')
{
 for($i=0, $captcha=''; $i<5; $i++) $captcha .= chr(mt_rand(48, 90));
 $_SESSION['captcha'] = $captcha;
 $captcha = imagecreatetruecolor(40, 20);
 $red = imagecolorallocate($captcha, 255, 0, 0);
 imagestring($captcha, 3, 3, 3, $_SESSION['captcha'], $red);
 imagepng($captcha);
 imagedestroy($captcha);
 exit();
}

//Admin Login
elseif($action == 'admin')
{
 include('newsscript/functions.php');
 include('newsscript/language_login.php');
 $_SESSION['dispall'] = false;
 $user = @array_map('trim', array_slice(file($newspwsdat), 1)) or die($lang['login']['nouser']);
 if(isset($_POST['name']) && ($key = unifyUser($_POST['name'] = stripEscape($_POST['name']))) !== false) //Nutzer holen
 {
  unset($_POST['name']);
  $value = explode("\t", $user[$key]);
  $_SESSION['newsname'] = $value[0];
  if($value[2] >= ($_POST['edit'] == 'script')) //Rechte checken
  {
   if($_POST['edit'] == 'newpw') //Neues PW?
   {
    for($i=0,$newpw=''; $i<10; $i++) $newpw .= chr(mt_rand(33, 126));
    $value[4] = md5($newpw);
    $user[$key] = implode("\t", $value);
    saveUser($newspwsdat);
    if(!@mail($value[3], $_SERVER['SERVER_NAME'] . ' Newsscript: ' . $lang['login']['subject'], sprintf($lang['login']['mail'], $_SESSION['newsname'], $_SERVER['REMOTE_ADDR'], $newpw), 'From: newsscript@' . $_SERVER['SERVER_NAME'] . "\n" . 'Reply-To: ' . $value[3] . "\n" . 'X-Mailer: PHP/' . phpversion() . "\n" . 'Content-Type: text/plain; charset=' . $lang['login']['charset'])) $_POST['edit'] = 'nopw';
   }
   else //News oder Script
   {
    $_POST['edit'] == 'script' ? $redir = 'newsscript/index.php' : $_SESSION['dispall'] = true;
    unset($_POST['edit']);
    $_SESSION['newspw'] = md5($_POST['newspw']);
    if(isset($value[4]) && $value[4] == $_SESSION['newspw']) //Neues PW checken
    {
     $value[1] = $_SESSION['newspw'];
     unset($value[4]);
     $user[$key] = implode("\t", $value);
     saveUser($newspwsdat);
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
    else unset($_SESSION['newspw'], $_SESSION['dispall']);
   }
  }
 }
 if(!isset($_SESSION['newspw']))
 {
  newsHead('CHS - Newsscript: ' . $lang['login']['title'], 'Newsscript, CHS, ' . $lang['login']['title'] . ', Chrissyx', $lang['login']['title'] . ' des Newsscript von CHS', $lang['login']['charset'], $lang['login']['code'], null, 'newsscript/style.css');
  ?>
  <h3>CHS - Newsscript: <?=$lang['login']['title']?></h3>
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <table>
   <tr><td><?=$lang['login']['name']?></td><td><input type="text" name="name" value="<?=isset($_POST['name']) && $_POST['name'] != '' ? $_POST['name'] : (isset($_SESSION['newsname']) ? $_SESSION['newsname'] : '')?>" <?php
  if(isset($_POST['name'])) echo('style="border-color:#FF0000;" /></td></tr>
   <tr><td colspan="2"><span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['unknown'] . '</span><br ');?>/></td></tr>
   <tr><td><?=$lang['login']['pass']?></td><td><input type="password" name="newspw" <?php
  if(isset($_POST['newspw']) && !isset($_POST['edit'])) echo('style="border-color:#FF0000;" /></td></tr>
   <tr><td colspan="2"><span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['wrongpass'] . '</span><br ');?>/></td></tr>
  </table>
  <input type="radio" name="edit" value="newpw" /><?=$lang['login']['reqpass']?>
<?php
  if(!isset($_POST['name']))
  {
   if(isset($_POST['edit']) && $_POST['edit'] == 'newpw') echo ('<br />
  <span class="green">&raquo; ' . $lang['login']['sendpass'] . '</span><br />');
   elseif(isset($_POST['edit']) && $_POST['edit'] == 'nopw') echo('<br />
  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['sendnopass'] . '</span><br />');
  }
?><br />
  <input type="radio" name="edit" value="news" checked="checked" /><?=$lang['login']['news']?><br />
  <input type="radio" name="edit" value="script" /><?=$lang['login']['script']?>
<?php
  if(isset($_POST['edit']) && $_POST['edit'] == 'script' && !isset($_POST['name'])) echo('<br />
  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['login']['norights'] . '</span><br />');
?><br />
  <input type="submit" value="<?=$lang['login']['login']?>" />
  <input type="hidden" name="action" value="admin" />
  </form>
  <?php
  newsTail();
  exit();
 }
}

//Admin Logout
elseif($action == 'newsout') unset($_SESSION['newsname'], $_SESSION['newspw'], $_SESSION['newsadmin'], $_SESSION['dispall']);

//News lesen ----------------------------------------------------------------------------------------------------------------------------------------
include_once('newsscript/language_news.php');
$newsTemplate = '  <div %s>
   <strong style="float:left; font-size:medium;">%s</strong>%s<br style="clear:left;" />
   <span style="font-size:small;">' . $lang['news']['postedby'] . ' %s &ndash; %s &ndash; %s ' . $lang['news']['oclock'] . ' &ndash; ' . $lang['news']['cat'] . ' %s</span>
   <hr size="1" noshade="noshade" />
   %s
   <hr size="1" noshade="noshade" />
   %s<span style="font-size:small;">' . $lang['news']['sources'] . ' %s</span>
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
  if(($user = getUser($_SESSION['newsname'])) == false) die($lang['news']['unknown']);
  elseif($user[1] != $_SESSION['newspw']) die($lang['news']['wrongpass']);
  elseif(!$_SESSION['dispall']) die($lang['news']['norights']);
 }

 foreach($news as $key => $value) if(current(sscanf($value, "%s")) != $_GET['newsid']) continue;
 else
 {
  $value = explode("\t", trim($news[$key]));
//News löschen
  if($action == 'delete')
  {
   unset($news[$key]);
   saveNews();
   if(file_exists($newscomments . $_GET['newsid'] . '.dat')) unlink($newscomments . $_GET['newsid'] . '.dat');
   echo('<div style="width:99%; text-align:center;"><p style="color:#008000; font-size:large;">' . $lang['news']['deletenews'] . "</p>\n<p><a href=\"" . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '">' . $lang['news']['backtopage'] . "</a></p></div><br />\n");
  }
//News editieren
  elseif($action == 'edit')
  {
   if(!isset($_POST['headline'], $_POST['srcarray'], $_POST['newsbox'], $_POST['newsbox2']))
   {
    list(, , , , $_POST['cat'], $_POST['headline'], $_POST['srcarray'], $_POST['newsbox'], $_POST['newsbox2']) = $value;
    //Alles wieder dekodieren
    $_POST['headline'] = strtr($_POST['headline'], array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))+array('&#039;' => "'")); #htmlspecialchars_decode() ab PHP5
    $_POST['srcarray'] = ($_POST['srcarray'] ? "\t" : '') . str_replace(array(' ', '&amp;'), array("\t", '&'), $_POST['srcarray']);
    $_POST['newsbox'] = strtr(str_replace(array('<br />', '<br/>', '<br>'), "\n", $_POST['newsbox']), array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))+array('&#039;' => "'")); #htmlspecialchars_decode() ab PHP5
    $_POST['newsbox2'] = strtr(str_replace(array('<br />', '<br/>', '<br>'), "\n", $_POST['newsbox2']), array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))+array('&#039;' => "'")); #htmlspecialchars_decode() ab PHP5
    $_POST['preview'] = true;
   }
   $_POST['srcarray'] = explode("\t", $_POST['srcarray']);
   showJS();
   ?>
<form name="newsform" id="newsform" action="<?=$_SERVER['PHP_SELF']?>?newsid=<?=$value[0]?>&amp;page=<?=$_GET['page']?>&amp;action=edit" method="post" onsubmit="addSource();">
<div style="background-color:#99CCFF; font-family:Arial,sans-serif; width:99%; border:1px solid #000000; padding:5px;">
 <h4>&raquo; <?=$lang['news']['editnews']?></h4>
<?php
//Editieren Vorschau
  if($_POST['preview'])
   echo(sprintf($newsTemplate,
                'style="border:medium double #000000; padding:5px;"', //Style
                preg_replace($bbcode1, $bbcode2, strtr(stripEscape($_POST['headline']), $smilies)), //Überschrift
                $cats[$_POST['cat']][1] ? ' <img src="' . $cats[$_POST['cat']][1] . '" alt="' . $cats[$_POST['cat']][0] . '" style="float:right; margin-left:5px;" />' : '', //Katbild
                $value[3], //Autor
                date($lang['news']['DATEFORMAT'], $value[1]), //Datum
                date($lang['news']['TIMEFORMAT'], $value[1]), //Uhrzeit
                $cats[$_POST['cat']][0], //Kategorie
                preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox']))), $smilies)), //News
                $_POST['newsbox2'] ? preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox2']))), $smilies)) . "<hr noshade=\"noshade\" style=\"height:1px;\" />\n" : null, //Weiterlesen
                (isset($_POST['srcarray'][1]) ? '<select style="width:100px; font-size:x-small;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;' . str_replace('&', '&amp;', implode('</option><option>', $_POST['srcarray'])) . '</option></select>' : $lang['news']['non']) . ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '">' . ($_POST['newsbox2'] ? $lang['news']['readon'] . ' / ' : '') . (file_exists($newscomments . $value[0] . '.dat') ? $lang['news']['comments'] . ' ( <strong>' . count(file($newscomments . $value[0] . '.dat')) . '</strong> )' : $lang['news']['writecomment']) . '</a>'
               ));
//Editieren posten
  elseif($_POST['update'])
  {
   $temp = '  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['news']['fillout'] . '</span><br /><br />';
   if($_POST['headline'] && $_POST['newsbox'])
   {
    #id - timestamp - ip - usrid?name? - catid - headline - quellen - text - weiterlesen
    $news[$key] = $value[0] . "\t" . $value[1] . "\t" . $value[2] . "\t" . $value[3] . "\t" . $_POST['cat'] . "\t" . stripEscape($_POST['headline']) . "\t" . str_replace('&', '&amp;', implode(' ', array_slice($_POST['srcarray'], 1))) . "\t" . ereg_replace("(\r)(\n)", '' , nl2br(stripEscape(trim($_POST['newsbox']) . "\t" . trim($_POST['newsbox2']))));
    saveNews();
    $temp = '   <span style="color:#008000; font-weight:bold;">&raquo; ' . $lang['news']['newsup'] . '</span> <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '">' . $lang['news']['back'] . '</a><br /><br />';
   }
  }
  else $temp = '';
//News bearbeiten
  echo($temp . $lang['news']['headline']);
  ?>
 <input type="text" name="headline" value="<?=stripEscape($_POST['headline'])?>" size="65" onclick="activeNewsbox = this.name;" /><br />
 <input type="button" value="B" style="font-weight:bold; width:25px;" onclick="setNewsTag('[b]', '[/b]');" /> <input type="button" value="I" style="font-style:italic; width:25px;" onclick="setNewsTag('[i]', '[/i]');" /> <input type="button" value="U" style="text-decoration:underline; width:25px;" onclick="setNewsTag('[u]', '[/u]');" /> <input type="button" value="S" style="text-decoration:line-through; width:25px;" onclick="setNewsTag('[s]', '[/s]');" /> <input type="button" value="CENTER" style="width:70px;" onclick="setNewsTag('[center]', '[/center]');" /> <input type="button" value="QUOTE" style="width:65px;" onclick="setNewsTag('[quote]', '[/quote]');" /> <input type="button" value="URL" style="width:40px;" onclick="setNewsTag('[url]', '[/url]');" /> <input type="button" value="IMG" style="width:40px;" onclick="setNewsTag('[img]', '[/img]');" /> <select style="width:85px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[color=' + this.options[this.options.selectedIndex].value + ']', '[/color]');">
  <option>COLOR</option>
  <option value="#000000" style="background-color:#000000; color:#000000;"><?=$lang['news']['black']?></option>
  <option value="#808080" style="background-color:#808080; color:#808080;"><?=$lang['news']['dark_grey']?></option>
  <option value="#800000" style="background-color:#800000; color:#800000;"><?=$lang['news']['dark_red']?></option>
  <option value="#FF0000" style="background-color:#FF0000; color:#FF0000;"><?=$lang['news']['red']?></option>
  <option value="#008000" style="background-color:#008000; color:#008000;"><?=$lang['news']['dark_green']?></option>
  <option value="#00FF00" style="background-color:#00FF00; color:#00FF00;"><?=$lang['news']['light_green']?></option>
  <option value="#808000" style="background-color:#808000; color:#808000;"><?=$lang['news']['ochre']?></option>
  <option value="#FFFF00" style="background-color:#FFFF00; color:#FFFF00;"><?=$lang['news']['yellow']?></option>
  <option value="#000080" style="background-color:#000080; color:#000080;"><?=$lang['news']['dark_blue']?></option>
  <option value="#0000FF" style="background-color:#0000FF; color:#0000FF;"><?=$lang['news']['blue']?></option>
  <option value="#800080" style="background-color:#800080; color:#800080;"><?=$lang['news']['dark_purple']?></option>
  <option value="#FF00FF" style="background-color:#FF00FF; color:#FF00FF;"><?=$lang['news']['purple']?></option>
  <option value="#008080" style="background-color:#008080; color:#008080;"><?=$lang['news']['dark_turquoise']?></option>
  <option value="#00FFFF" style="background-color:#00FFFF; color:#00FFFF;"><?=$lang['news']['turquoise']?></option>
  <option value="#C0C0C0" style="background-color:#C0C0C0; color:#C0C0C0;"><?=$lang['news']['grey']?></option>
  <option value="#FFFFFF" style="background-color:#FFFFFF; color:#FFFFFF;"><?=$lang['news']['white']?></option>
 </select> <input type="button" value="FLASH" onclick="setNewsTag('[flash]', '[/flash]');" /><br />
 <textarea name="newsbox" id="newsbox" rows="10" cols="60" style="margin-bottom:5px; float:left;" onclick="activeNewsbox = this.name;"><?=stripEscape(trim($_POST['newsbox']))?></textarea>
<?php
if($smilies)
{
 echo(' <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;"><strong>' . $lang['news']['smilies'] . '</strong><br />');
 $i=0;
 foreach($smilies as $key => $value)
 {
  if($i >= $smiliesmax) break;
  if(($i++ % $smiliesmaxrow) == 0) echo("<br />\n");
  echo('  <a href="javascript:setNewsSmilie(\' ' . $key . '\');">' . $value . '</a>');
 }
 echo('<br /><br />
  <input type="button" value="' . $lang['news']['moresmilies'] . '" onclick="window.open(\'news.php?action=smilies\', \'_blank\', \'width=250, resizable, scrollbars, status\');" />
 </div>');
}
?><br style="clear:both;" />
 <?=$lang['news']['readontext']?> <input type="button" id="toggler" value="<?=($_POST['newsbox2']) ? $lang['news']['discard'] . ' &l' : $lang['news']['expand'] . ' &r'?>aquo;" onclick="toggleFullStory();" /><br />
 <textarea name="newsbox2" id="newsbox2" rows="10" cols="60" style="margin-bottom:5px; display:<?=($_POST['newsbox2']) ? 'inline' : 'none'?>;" onclick="activeNewsbox = this.name;"><?=stripslashes(trim($_POST['newsbox2']))?></textarea><br />
 <?=$lang['news']['sources']?> <input type="text" name="sources" id="sources" size="25" /> <a href="javascript:doSource(true);"><?=$lang['news']['add']?></a> &ndash; <a href="javascript:doSource(false);"><?=$lang['news']['remove']?></a><br />
 <input type="submit" value="<?=$lang['news']['update']?>" /> <input type="submit" name="preview" value="<?=$lang['news']['preview']?>" style="font-weight:bold;" /> <?=$lang['news']['cat']?> <select name="cat" style="width:125px;">
<?php foreach($cats as $key => $value) echo '  <option value="' . $key . '"' . ($key == $_POST['cat'] ? ' selected="selected"' : '') . '>' . $value[0] . '</option>'; ?>
 </select> <input type="button" value="<?=$lang['news']['cancel']?>" onclick="document.location='<?=$_SERVER['PHP_SELF'].'?page='.$_GET['page']?>'" />
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
    if(!($_POST['name'] = htmlspecialchars(stripslashes($_POST['name']), ENT_QUOTES))) $_POST['name'] .= '" style="border-color:#FF0000;';
    elseif($captcha && $_POST['captcha'] != $_SESSION['captcha']) $_POST['captcha'] = 'border-color:#FF0000; ';
    elseif($_POST['newsbox'])
    {
     #todo: still buggy regex
     $_POST['newsbox'] = preg_replace_callback("/^([^\]]+?:\/\/|www\.)[^ \[\.]+(\.[^ \[\.]+)+/si", create_function('$arr', "return (\$arr[2]) ? '[url]' . ((\$arr[1] == 'www.') ? 'http://' : '') . \$arr[0] . '[/url]' : \$arr[0];"), $_POST['newsbox']);
     $temp = fopen($newscomments . $_GET['newsid'] . '.dat', 'a');
     fwrite($temp, time() . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_POST['name'] . "\t" . ereg_replace("(\r)(\n)", '' , nl2br(htmlspecialchars(stripslashes(trim($_POST['newsbox'])), ENT_QUOTES))) . "\n");
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
    if(count($towrite) == 0) unlink($newscomments . $_GET['newsid'] . '.dat');
    else
    {
     $temp = fopen($newscomments . $_GET['newsid'] . '.dat', 'w');
     fwrite($temp, implode("\n", $towrite) . "\n");
     fclose($temp);
	}
    $temp = '   <span style="color:#008000;">&raquo; ' . $lang['news']['delcomment'] . '</span><br /><br />';
   }
   else unset($temp);
   ?>

  <script type="text/javascript">
  function setNewsSmilie(smilie)
  {
   document.getElementById('newsbox').value += smilie;
   document.getElementById('newsbox').focus();
  }
  </script>

<?=sprintf($newsTemplate,
           'style="width:99%; border:1px solid #000000; padding:5px;"', //Style
           preg_replace($bbcode1, $bbcode2, strtr($value[5], $smilies)), //Überschrift
           $cats[$value[4]][1] ? '<img src="' . $cats[$value[4]][1] . '" alt="' . $cats[$value[4]][0] . '" style="margin-left:5px; border:none; float:right;" />' : '', //Katbild
           $value[3], //Autor
           date($lang['news']['DATEFORMAT'], $value[1]), //Datum
           date($lang['news']['TIMEFORMAT'], $value[1]), //Uhrzeit
           $cats[$value[4]][0], //Kategorie
           preg_replace($bbcode1, $bbcode2, strtr($value[7], $smilies)), //News
           isset($value[8]) && $value[8] != '' ? preg_replace($bbcode1, $bbcode2, strtr($value[8], $smilies)) . '<hr size="1" noshade="noshade" />' . "\n" : null, //Weiterlesen
           (isset($value[6][1]) && $value[6][1] ? ' <select style="width:100px; font-size:x-small;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;</option><option>' . str_replace(' ', '</option><option>', $value[6]) . '</option></select>' : $lang['news']['non']) . ($_SESSION['dispall'] ? ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;action=edit">' . $lang['news']['edit'] . '</a> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;action=delete" onclick="return confirm(\'' . $lang['news']['confirm'] . '\');">' . $lang['news']['delete'] . '</a>' : '')
          )?>

  <p><a href="<?=$redir ? $redir : '.'?>?page=<?=$_GET['page']?>">&laquo; <?=$lang['news']['backtoall']?></a></p>
  <div class="newsscriptcomments" style="width:99%; border:1px solid #000000; padding:5px;">
   <h4>&raquo; <?=$lang['news']['comments']?></h4>
<?php //Kommentare auslesen
   if(!file_exists($newscomments . $_GET['newsid'] . '.dat')) echo('   <p>' . $lang['news']['noyet'] ."</p>\n");
   else foreach(file($newscomments . $_GET['newsid'] . '.dat') as $key => $value)
        {
         $value = explode("\t", $value);
         echo('<span style="font-style:italic;"><strong>' . $value[2] . '</strong> ' . $lang['news']['onday'] . ' <strong>' . date($lang['news']['DATEFORMAT'], $value[0]) . '</strong> ' . $lang['news']['atclock'] . ' <strong>' . date($lang['news']['TIMEFORMAT'], $value[0]) . "</strong>:</span><br />\n" . preg_replace($bbcode1, $bbcode2, strtr($value[3], $smilies)) . (($_SESSION['dispall']) ? "<br />\nIP: <strong>" . $value[1] . '</strong> &ndash; [ <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $_GET['newsid'] . '&amp;page=' . $_GET['page'] . '&amp;action=delcomment&amp;id=' . $key . '#box">' . $lang['news']['delete'] . '</a> ]' : '') . '<hr size="1" noshade="noshade" />' . "\n");
        }
   echo($temp . "\n");
?>
   <form action="<?=$_SERVER['PHP_SELF']?>?newsid=<?=$_GET['newsid']?>&amp;page=<?=$_GET['page']?>&amp;action=comment#box" method="post">
   <div id="box" style="float:left;"> 
    <?=$lang['news']['name']?> <input type="text" name="name" value="<?=!$_POST['name'] ? (!$_SERVER['newsname'] ? $_SESSION['shoutName'] : $_SERVER['newsname']) : $_POST['name']?>" size="30" /><br />
    <textarea name="newsbox" id="newsbox" rows="5" cols="30"><?=htmlspecialchars(stripslashes(trim($_POST['newsbox'])), ENT_QUOTES)?></textarea><br />
<?=$captcha ? '    <input type="text" name="captcha" style="' . $_POST['captcha'] . 'vertical-align:middle;" /> &larr; <img src="' . $_SERVER['PHP_SELF'] . '?action=captcha" alt="CAPTCHA" style="vertical-align:middle;"><br />' . "\n" : null?>
    <input type="submit" value="<?=$lang['news']['docomment']?>" style="font-weight:bold;" /> <input type="reset" value="<?=$lang['news']['reset']?>" />
   </div>
<?php
if($smilies)
{
 echo('   <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;">
    <strong>' . $lang['news']['smilies'] . '</strong><br />');
 $i=0;
 foreach($smilies as $key => $value)
 {
  if($i >= $smiliesmax) break;
  if(($i++ % $smiliesmaxrow) == 0) echo("<br />\n");
  echo('    <a href="javascript:setNewsSmilie(\' ' . strtr($key, $htmlJSDecode) . '\');">' . $value . '</a>');
 }
 echo('<br /><br />
    <input type="button" value="' . $lang['news']['moresmilies'] . '" onclick="window.open(\'news.php?action=smilies\', \'_blank\', \'width=250, resizable, scrollbars, status\');" />
   </div>');
}?><br style="clear:left;" />
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
  if(($user = getUser($_SESSION['newsname'])) == false) die($lang['news']['unknown']);
  elseif($user[1] != $_SESSION['newspw']) die($lang['news']['wrongpass']);
  else $_POST['srcarray'] = isset($_POST['srcarray']) ? explode("\t", $_POST['srcarray']) : array('');
  showJS();
  ?>
<form name="newsform" id="newsform" action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="addSource();">
<div style="background-color:#99CCFF; font-family:Arial,sans-serif; width:99%; border:1px solid #000000; padding:5px;">
 <h4>&raquo; <?=$lang['news']['addnews']?></h4>
<?php
//News Vorschau
  if(isset($_POST['preview']))
   echo(sprintf($newsTemplate,
                'style="border:medium double #000000; padding:5px;"', //Style
                preg_replace($bbcode1, $bbcode2, strtr(stripEscape($_POST['headline']), $smilies)), //Überschrift
                $cats[$_POST['cat']][1] ? ' <img src="' . $cats[$_POST['cat']][1] . '" alt="' . $cats[$_POST['cat']][0] . '" style="margin-left:5px; border:none; float:right;" />' : '', //Katbild
                $_SESSION['newsname'], //Autor
                date($lang['news']['DATEFORMAT']), //Datum
                date($lang['news']['TIMEFORMAT']), //Uhrzeit
                $cats[$_POST['cat']][0], //Kategorie
                preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox']))), $smilies)), //News
                $_POST['newsbox2'] ? preg_replace($bbcode1, $bbcode2, strtr(nl2br(stripEscape(trim($_POST['newsbox2']))), $smilies)) . '<hr size="1" noshade="noshade" />' . "\n" : null, //Weiterlesen
                (isset($_POST['srcarray'][1]) ? '<select style="width:100px; font-size:x-small;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;' . str_replace('&', '&amp;', implode('</option><option>', $_POST['srcarray']/*array_map('substr', $_POST['srcarray'], array_fill(0, $size, 0), array_fill(0, $size, 20))*/)) . '</option></select>' : $lang['news']['non']) . ' &ndash; <a href="#">' . ($_POST['newsbox2'] ? $lang['news']['readon'] . ' / ' : '') . $lang['news']['writecomment'] . '</a>'
               ));
//News posten
  elseif(isset($_POST['update']))
  {
   $temp = '  <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['news']['fillout'] . '</span><br /><br />';
   if($_POST['headline'] && $_POST['newsbox'])
   {
    array_unshift($news, ++$news[0]);
    #id - timestamp - ip - usrid?name? - catid - headline - quellen - text - weiterlesen
    $news[1] = /*(@current(sscanf($news[0], "%d\t[.*]"))+1)*/ ($news[0]-1) . "\t" . time() . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SESSION['newsname'] . "\t" . $_POST['cat'] . "\t" . stripEscape($_POST['headline']) . "\t" . str_replace('&', '&amp;', implode(' ', array_slice($_POST['srcarray'], 1))) . "\t" . ereg_replace("(\r)(\n)", '' , nl2br(stripEscape(trim($_POST['newsbox']) . "\t" . trim($_POST['newsbox2']))));
    saveNews();
    unset($_POST['cat'], $_POST['headline'], $_POST['srcarray'], $_POST['newsbox'], $_POST['newsbox2']);
    $temp = '   <span style="color:#008000; font-weight:bold;">&raquo; ' . $lang['news']['newspost'] . '</span><br /><br />';
   }
  }
  else $temp = '';
//News erstellen
  echo($temp . $lang['news']['headline']);
  ?> <input type="text" name="headline" value="<?=stripEscape($_POST['headline'])?>" size="65" onclick="activeNewsbox = this.name;" /><br />
 <input type="button" value="B" style="font-weight:bold; width:25px;" onclick="setNewsTag('[b]', '[/b]');" /> <input type="button" value="I" style="font-style:italic; width:25px;" onclick="setNewsTag('[i]', '[/i]');" /> <input type="button" value="U" style="text-decoration:underline; width:25px;" onclick="setNewsTag('[u]', '[/u]');" /> <input type="button" value="S" style="text-decoration:line-through; width:25px;" onclick="setNewsTag('[s]', '[/s]');" /> <input type="button" value="CENTER" style="width:70px;" onclick="setNewsTag('[center]', '[/center]');" /> <input type="button" value="QUOTE" style="width:65px;" onclick="setNewsTag('[quote]', '[/quote]');" /> <input type="button" value="URL" style="width:40px;" onclick="setNewsTag('[url]', '[/url]');" /> <input type="button" value="IMG" style="width:40px;" onclick="setNewsTag('[img]', '[/img]');" /> <select style="width:85px;" onchange="if(this.options.selectedIndex != 0) setNewsTag('[color=' + this.options[this.options.selectedIndex].value + ']', '[/color]');">
  <option>COLOR</option>
  <option value="#000000" style="background-color:#000000; color:#000000;"><?=$lang['news']['black']?></option>
  <option value="#808080" style="background-color:#808080; color:#808080;"><?=$lang['news']['dark_grey']?></option>
  <option value="#800000" style="background-color:#800000; color:#800000;"><?=$lang['news']['dark_red']?></option>
  <option value="#FF0000" style="background-color:#FF0000; color:#FF0000;"><?=$lang['news']['red']?></option>
  <option value="#008000" style="background-color:#008000; color:#008000;"><?=$lang['news']['dark_green']?></option>
  <option value="#00FF00" style="background-color:#00FF00; color:#00FF00;"><?=$lang['news']['light_green']?></option>
  <option value="#808000" style="background-color:#808000; color:#808000;"><?=$lang['news']['ochre']?></option>
  <option value="#FFFF00" style="background-color:#FFFF00; color:#FFFF00;"><?=$lang['news']['yellow']?></option>
  <option value="#000080" style="background-color:#000080; color:#000080;"><?=$lang['news']['dark_blue']?></option>
  <option value="#0000FF" style="background-color:#0000FF; color:#0000FF;"><?=$lang['news']['blue']?></option>
  <option value="#800080" style="background-color:#800080; color:#800080;"><?=$lang['news']['dark_purple']?></option>
  <option value="#FF00FF" style="background-color:#FF00FF; color:#FF00FF;"><?=$lang['news']['purple']?></option>
  <option value="#008080" style="background-color:#008080; color:#008080;"><?=$lang['news']['dark_turquoise']?></option>
  <option value="#00FFFF" style="background-color:#00FFFF; color:#00FFFF;"><?=$lang['news']['turquoise']?></option>
  <option value="#C0C0C0" style="background-color:#C0C0C0; color:#C0C0C0;"><?=$lang['news']['grey']?></option>
  <option value="#FFFFFF" style="background-color:#FFFFFF; color:#FFFFFF;"><?=$lang['news']['white']?></option>
 </select> <input type="button" value="FLASH" onclick="setNewsTag('[flash]', '[/flash]');" /><br />
 <textarea name="newsbox" id="newsbox" rows="10" cols="60" style="margin-bottom:5px; float:left;" onclick="activeNewsbox = this.name;"><?=stripEscape(trim($_POST['newsbox']))?></textarea>
<?php
if($smilies)
{
 echo(' <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;"><strong>' . $lang['news']['smilies'] . '</strong><br />');
 $i=0;
 foreach($smilies as $key => $value)
 {
  if($i >= $smiliesmax) break;
  if(($i++ % $smiliesmaxrow) == 0) echo("<br />\n");
  echo('  <a href="javascript:setNewsSmilie(\' ' . strtr($key, $htmlJSDecode) . '\');">' . $value . '</a>');
 }
 echo('<br /><br />
  <input type="button" value="' . $lang['news']['moresmilies'] . '" onclick="window.open(\'news.php?action=smilies\', \'_blank\', \'width=250, resizable, scrollbars, status\');" />
 </div>');
}
?><br style="clear:both;" />
 <?=$lang['news']['readontext']?> <input type="button" id="toggler" value="<?=$_POST['newsbox2'] ? $lang['news']['discard'] . ' &l' : $lang['news']['expand'] . ' &r'?>aquo;" onclick="toggleFullStory();" /><br />
 <textarea name="newsbox2" id="newsbox2" rows="10" cols="60" style="margin-bottom:5px; display:<?=$_POST['newsbox2'] ? 'inline' : 'none'?>;" onclick="activeNewsbox = this.name;"><?=stripslashes(trim($_POST['newsbox2']))?></textarea><br />
 <?=$lang['news']['sources']?> <input type="text" name="sources" id="sources" size="25" /> <a href="javascript:doSource(true);"><?=$lang['news']['add']?></a> &ndash; <a href="javascript:doSource(false);"><?=$lang['news']['remove']?></a><br />
 <input type="submit" value="<?=$lang['news']['postnews']?>" /> <input type="submit" name="preview" value="<?=$lang['news']['preview']?>" style="font-weight:bold;" /> <!--<input type="reset" value="Reset" />--> <?=$lang['news']['cat']?> <select name="cat" style="width:125px;">
<?php foreach($cats as $key => $value) echo('  <option value="' . $key . '"' . ($key == $_POST['cat'] ? ' selected="selected"' : '') . '>' . $value[0] . '</option>'); ?>
 </select> <input type="button" value="<?=$lang['news']['logout']?>" onclick="document.location='<?=$_SERVER['PHP_SELF']?>?action=newsout'" />
 <input type="hidden" name="update" value="true" />
 <input type="hidden" name="srcarray" id="srcarray" value="" />
</div>
</form>
<br />
  <?php
 }

 if(!isset($news[1])) echo('<div class="newsscriptmain" style="width:99%; text-align:center;">' . $lang['news']['nofound'] . "</div><br />\n");
 else
 {
//News zeigen
  $size = count($news = array_slice($news, 1));
  $_GET['page'] = !isset($_GET['page']) ? '' : ($_GET['page'] < 0 ? 0 : (($_GET['page']*$newsmax >= $size) ? $_GET['page']-1 : $_GET['page']));
  $start = $_GET['page']*$newsmax;
  $end = (($size-$start) > $newsmax) ? $start+$newsmax : $size;
  for($i=$start; $i<$end; $i++)
  {
   $value = explode("\t", $news[$i]);
   /*if($_GET['catid'] && $_GET['catid'] != $value[4])
   {
    $end++;
    continue;
   }*/
   echo(sprintf($newsTemplate,
                'class="newsscriptmain" style="width:99%; border:1px solid #000000; padding:5px;"', //Style
                preg_replace($bbcode1, $bbcode2, strtr($value[5], $smilies)), //Überschrift
                $cats[$value[4]][1] ? '<img src="' . $cats[$value[4]][1] . '" alt="' . $cats[$value[4]][0] . '" style="float:right; margin-left:5px;" />' : '', //Katbild
                $value[3], //Autor
                date($lang['news']['DATEFORMAT'], $value[1]), //Datum
                date($lang['news']['TIMEFORMAT'], $value[1]), //Uhrzeit
                $cats[$value[4]][0], //Kategorie
                preg_replace($bbcode1, $bbcode2, strtr($value[7], $smilies)), //News
                null, //Weiterlesen
                ($value[6] ? '<select style="font-size:x-small; width:100px;" onchange="if(this.options.selectedIndex != 0) window.open(this.options[this.options.selectedIndex].text, \'_blank\'); else return false;"><option>&emsp;&emsp;&emsp;&ensp;&darr;</option><option>' . str_replace(' ', '</option><option>', $value[6]) . '</option></select>' : $lang['news']['non']) . ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '">' . ($value[8] ? $lang['news']['readon'] . ' / ' : '') . (file_exists($newscomments . $value[0] . '.dat') ? $lang['news']['comments'] . ' ( <strong>' . count(file($newscomments . $value[0] . '.dat')) . '</strong> )' : $lang['news']['writecomment']) . '</a>' . (isset($_SESSION['dispall']) && $_SESSION['dispall'] === true ? ' &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;action=edit">' . $lang['news']['edit'] . '</a> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?newsid=' . $value[0] . '&amp;page=' . $_GET['page'] . '&amp;action=delete" onclick="return confirm(\'' . $lang['news']['confirm'] . '\');">' . $lang['news']['delete'] . '</a>' : '')
               ));
  }
  echo('  <div class="newsscriptfooter" style="width:99%; text-align:center; font-size:small;">
  <a href="' . $_SERVER['PHP_SELF'] . '?page=0">&laquo;</a> <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($_GET['page']-1) . '">&lsaquo; ' . $lang['news']['prev'] . '</a> &ndash; <select onchange="document.location=\'' . $_SERVER['PHP_SELF'] . '?page=\' + this.options[this.options.selectedIndex].value;" style="font-size:x-small; vertical-align:middle;">
');
  for($i=0; $i<ceil($size/$newsmax); $i++) echo('   <option value="' . $i . '"' . ($i == $_GET['page'] ? ' selected="selected"' : '') . '>' . $lang['news']['page'] . ' ' . ($i+1) . "</option>\n");
  echo('  </select> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($_GET['page']+1) . '">' . $lang['news']['next'] . ' &rsaquo;</a> <a href="' . $_SERVER['PHP_SELF'] . '?page=' . floor($size/$newsmax) .'">&raquo;</a><br />
  ' . sprintf($lang['news']['showing'], ($start+1), (($end > $size) ? $size : $end), $size) . "\n </div><br />\n ");
 }
}
#PLEASE DON'T REMOVE THIS!
?><div style="width:99%; text-align:center; font-size:xx-small;">Powered by CHS - Newsscript<br />&copy; 2008, 2009 by Chrissyx</div>