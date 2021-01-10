<?php
/**
 * Update-Routine für alle Versionen. Aktualisiert sukzessive jede Version von der Vorhandenen bis zur Neusten.
 * 
 * @author Chrissyx
 * @copyright (c) 2001 - 2009 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Newsscript
 */
require('functions.php');

if(isset($_GET['inifile'])) parseLanguage($_GET['inifile']);
elseif(!isset($_POST['update']))
{
 newsHead('CHS - Newsscript: Choose language', 'Newsscript, CHS, choose, language, Chrissyx', 'Choose the language for the Newsscript from CHS', 'UTF-8', 'en');
 echo('  <div class="center" style="width:99%; border:1px solid #000000; padding:5px; margin-bottom:1%;">' . "\n" . '   <h3>Choose a language:</h3>' . "\n");
 foreach(glob('*.ini') as $value) echo('   <a href="' . $_SERVER['PHP_SELF'] . '?inifile=' . $value . '">' . $value . '</a><br />' . "\n");
 echo("  </div>\n  ");
 newsTail();
 exit();
}
include_once('language_news.php');

/**
 * Update von 1.0.2 auf 1.0.2.1
 * 
 * @since 1.0.2.1
 * @version 1.0.2.1
 */
function newsUpdate102()
{
 global $lang, $next;
 echo('  ' . $lang['news']['title'] . ' <span class="b">' . $next . " &rarr; 1.0.2.1</span>...<br />\n");
 //Nix zu tun
 $next = '';
}

/**
 * Update von 1.0.1 auf 1.0.2
 * Fixt Links mit Ankern, die in 1.0.1 getrennt wurden, und alle Entitäten. Bereitet Nutzung von CAPTCHA vor.
 * 
 * @since 1.0.2
 * @version 1.0.2.1
 */
function newsUpdate101()
{
 global $lang, $next;
 echo('  ' . $lang['news']['title'] . ' <span class="b">' . $next . " &rarr; 1.0.2</span>...<br />\n");
 list($newsdat, , $newspwsdat, , $newscatsdat, , $smiliesdat) = @array_map('trim', array_slice(file('settings.dat.php'), 1)) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
 //Update settings.dat
 $temp = fopen('settings.dat.php', 'a');
 fwrite($temp, "\n");
 fclose($temp);
 //Update smilies
 if($smiliesdat != '' && substr($smiliesdat, -4) != '.var' && file_exists('../' . $smiliesdat))
 {
  $smilies = array_map('trim', file('../' . $smiliesdat));
  foreach($smilies as &$value)
  {
   $value = explode("\t", $value);
   $value[1] = stripEscape($value[1]);
   $value = implode("\t", $value);
  }
  $smilies[0] = trim($smilies[0]);
  $temp = fopen('../' . $smiliesdat, 'w');
  fwrite($temp, implode("\n", $smilies));
  fclose($temp);
 }
 //Update cats
 $cats = array_map('trim', file('../' . $newscatsdat));
 $temp = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
 foreach($cats as &$value)
 {
  $value = explode("\t", $value);
  $value[1] = str_replace("'", $temp["'"], stripslashes($value[1]));
  $value = implode("\t", $value);
 }
 $cats[0] = trim($cats[0]);
 $temp = fopen('../' . $newscatsdat, 'w');
 fwrite($temp, implode("\n", $cats));
 fclose($temp);
 //Update user
 $user = @array_map('trim', array_slice(file('../' . $newspwsdat), 1)) or die($lang['news']['nouser']);
 foreach($user as &$value)
 {
  $value = explode("\t", $value);
  $value[0] = stripEscape($value[0]);
  $value = implode("\t", $value);
 }
 $temp = fopen('../' . $newspwsdat, 'w');
 fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . implode("\n", $user));
 fclose($temp);
 //Update news
 $news = array_map('trim', file('../' . $newsdat)) or die($lang['news']['nonews']);
 foreach($news as &$value)
 {
  $value = explode("\t", $value);
  $value[3] = stripEscape($value[3]);
  $value[6] = str_replace('&', '&amp;', $value[6]);
  $pos = 0;
  while(($pos = strpos($value[6], ' ', $pos)) !== false)
   if(($curSource = substr($value[6], ++$pos, 7)) != 'http://')
    $value[6] = str_replace(' ' . $curSource, '#' . $curSource, $value[6]);
  $value = implode("\t", $value);
 }
 $news[0] = trim($news[0]);
 $temp = fopen('../' . $newsdat, 'w');
 flock($temp, LOCK_EX);
 fwrite($temp, implode("\n", $news));
 flock($temp, LOCK_UN);
 fclose($temp);
 $next = '1.0.2';
}

/**
 * Update von 1.0 auf 1.0.1
 * Ersetzt alle # durch Leerzeichen in den Quellen.
 * 
 * @since 1.0.1
 * @version 1.0.2
 */
function newsUpdate10()
{
 global $lang, $next;
 echo('  ' . $lang['news']['title'] . ' <span class="b">' . $next . " &rarr; 1.0.1</span>...<br />\n");
 $newsdat = @current(array_map('trim', array_slice(file('settings.dat.php'), 1))) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
 $news = array_map('trim', file('../' . $newsdat)) or die($lang['news']['nonews']);
 foreach($news as &$value)
 {
  $value = explode("\t", $value);
  $value[6] = str_replace('#', ' ', $value[6]);
  $value = implode("\t", $value);
 }
 $news[0] = trim($news[0]);
 $temp = fopen('../' . $newsdat, 'w');
 flock($temp, LOCK_EX);
 fwrite($temp, implode("\n", $news));
 flock($temp, LOCK_UN);
 fclose($temp);
 $next = '1.0.1';
}

newsHead('CHS - Newsscript: ' . $lang['news']['update'], 'Newsscript, CHS, ' . $lang['news']['update'] . ', Chrissyx', $lang['news']['title'] . ' des Newsscript von CHS', $lang['news']['charset'], $lang['news']['code']);
$next = file_exists('version.dat.php') ? @end(file('version.dat.php')) : '1.0';
if(isset($_POST['update']) && $next != getNewsVersion())
{
 while($next != '') eval('newsUpdate' . str_replace('.', '', $next) . '();');
 $temp = fopen('version.dat.php', 'w');
 fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . getNewsVersion());
 fclose($temp);
 //Cache leeren
 if(file_exists('cats.php')) unlink('cats.php');
 if(file_exists('settings.php')) unlink('settings.php');
 if(file_exists('smilies.php')) unlink('smilies.php');
 echo("  <br />\n  " . $lang['news']['outro'] . "\n");
 unlink('language_install.php');
}
else echo('  <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
  <p>' . sprintf($lang['news']['intro'], $next, getNewsVersion()) . '</p>
  <p><input type="submit" value="' . $lang['news']['title'] . '" /></p>
  <input type="hidden" name="update" value="true" />
  </form>
  ');
newsTail();
?>