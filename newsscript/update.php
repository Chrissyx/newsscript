<?php
require('functions.php');

if($_GET['inifile']) parseLanguage($_GET['inifile']);
elseif(!$_POST['update'])
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
 * Update von 1.0 auf 1.0.1
 * 
 * @author Chrissyx
 * @copyright Chrissyx
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
 $next = '';
}

newsHead('CHS - Newsscript: ' . $lang['news']['update'], 'Newsscript, CHS, ' . $lang['news']['update'] . ', Chrissyx', $lang['news']['title'] . ' des Newsscript von CHS', $lang['news']['charset'], $lang['news']['code']);
$next = file_exists('version.dat.php') ? end(file('version.dat.php')) : '1.0';
if($_POST['update'] && $next != getNewsVersion())
{
 while($next != '') eval('newsUpdate' . str_replace('.', '', $next) . '();');
 $temp = fopen('version.dat.php', 'w');
 fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . getNewsVersion());
 fclose($temp);
 echo("  <br />\n  " . $lang['news']['outro'] . "\n");
 unlink('language_install.php');
}
else echo('  <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
  ' . sprintf($lang['news']['intro'], $next, getNewsVersion()) . '<br /><br />
  <input type="submit" value="' . $lang['news']['title'] . '" />
  <input type="hidden" name="update" value="true" />
  </form>
');
newsTail();
?>