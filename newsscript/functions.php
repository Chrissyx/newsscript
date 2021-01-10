<?php
/**
 * Benötigte Funktionen und initiale Anweisungen.
 * 
 * @author Chrissyx
 * @copyright (c) 2001 - 2009 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Newsscript
 */
//$action laden
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

//echo Kurzform aktivieren
if(ini_get('short_open_tag') == '0') ini_set('short_open_tag', '1');

//Session laden, IP sichern
session_start();
if(!isset($_SESSION['session_ip'])) $_SESSION['session_ip'] = $_SERVER['REMOTE_ADDR'];
else if($_SESSION['session_ip'] != $_SERVER['REMOTE_ADDR']) die('Nicht erlaubt, diese Session zu verwenden!');

//Aufbauzeit [PHP4]
$_SESSION['microtime'] = explode(' ', microtime());
$_SESSION['microtime'] = $_SESSION['microtime'][1] + $_SESSION['microtime'][0];

//Funktionen
/**
 * Generiert den XHTML Head für jede Seite und sendet den passenden Content-Type, wenn der Browser XML unterstützt.
 * 
 * @param string $title Der Titel des Dokuments
 * @param string $keywords Metatag für Schlüsselwörter
 * @param string $description Metatag für Beschreibung
 * @param string $charset Verwendeter Zeichensatz bzw. Kodierung
 * @param string $lang Sprachkürzel
 * @param string $htmlzusatz Zusätzliche Angaben zum <html> Tag, mit Leerzeichen beginnen!
 * @param string $stylesheet Die zu benutzende CSS Datei
 * @param string $sonstiges Weitere optionale XHTML Tags im Head
 * @param string $bodyzusatz Zusätzliche optionale Angaben zum <body> Tag, mit Leerzeichen beginnen!
 * @see newsTail()
 * @version 1.0
 */
function newsHead($title, $keywords, $description, $charset='ISO-8859-1', $lang='de', $htmlzusatz=null, $stylesheet='style.css', $sonstiges=null, $bodyzusatz=null)
{
 if(stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml')) header('Content-Type: application/xhtml+xml');
 echo('<?xml version="1.0" encoding="' . $charset . '" standalone="no" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="' . $lang . '" xml:lang="' . $lang . '"' . $htmlzusatz . '>
 <head>
  <title>' . $title . '</title>
  <meta name="author" content="Chrissyx" />
  <meta name="copyright" content="Chrissyx" />
  <meta name="keywords" content="' . $keywords . '" />
  <meta name="description" content="' . $description . '" />
  <meta name="robots" content="all" />
  <meta name="revisit-after" content="14 days" />
  <meta name="generator" content="Notepad 4.10.1998" />
  <meta http-equiv="content-language" content="' . $lang . '" />
  <meta http-equiv="content-type" content="application/xhtml+xml; charset=' . $charset . '" />
  <meta http-equiv="content-style-type" content="text/css" />
  <meta http-equiv="content-script-type" content="text/javascript" />
  <link rel="stylesheet" media="all" href="' . $stylesheet . '" />
');
 if($sonstiges) echo("  $sonstiges\n");
 echo(' </head>
 <body' . $bodyzusatz . '>
  <a id="top" name="top"></a>
');
}

/**
 * Generiert abschliessende Tags eines XHTML Dokuments und zeigt die Aufbauzeit an.
 * 
 * @see newsHead()
 * @version 1.0
 */
function newsTail()
{
 $temp = explode(' ', microtime());
 $temp = ($temp[1] + $temp[0]) - $_SESSION['microtime'];
 echo('<div class="center" style="clear:both; width:99%;">' . newsFont(1) . 'Seitenaufbauzeit: ' . round($temp, 4) . ' Sekunden</span></div>
 </body>
</html>');
}

/**
 * Gibt das CSS-Äquivalent zur HTML Schriftgröße zurück. <b>Nicht vergessen: </span>!</b>
 * 
 * @param int $wert HTML Schriftgröße von 1 bis 7 oder eigener Wert.
 * @return string span-Element mit gewählter Schriftgröße
 * @version 1.0.2
 */
function newsFont($wert)
{
 switch($wert)
 {
  case 7:
  return '<span style="font-size:300%;">';
  break;

  case 6:
  return '<span style="font-size:xx-large;">';
  break;

  case 5:
  return '<span style="font-size:x-large;">';
  break;

  case 4:
  return '<span style="font-size:large;">';
  break;

  case 3:
  return '<span style="font-size:medium;">';
  break;

  case 2:
  return '<span style="font-size:small;">';
  break;

  case 1.5:
  return '<span style="font-size:x-small;">';
  break;

  case 1:
  return '<span style="font-size:xx-small;">';
  break;

  default:
  return '<span style="font-size:' . $wert . ';">';
  break;
 }
}                      

/**
 * Überprüft, ob ein Benutzer mit dem Namen $name schon existiert.
 * 
 * @param string $name Name des zu suchenden Nutzers
 * @return int|bool Position im $user-Array im Erfolgsfall, ansonsten false
 * @version 1.0
 */
function unifyUser($name)
{
 global $user;
 foreach($user as $key => $value)
 {
  $value = explode("\t", $value);
  if($value[0] == $name) return $key;
 }
 return false;
}

/**
 * Gibt alle Daten zu einem Benutzer $name zurück.
 * 
 * @param string $name Name des Nutzers
 * @return mixed Array mit Daten, ansonsten false
 * @version 1.0
 */
function getUser($name)
{
 global $user;
 foreach($user as $value)
 {
  $value = explode("\t", $value);
  if($value[0] == $name) return $value;
 }
 return false;
}

/**
 * Speichert das aktuelle $user-Array in $file ab.
 * 
 * @param string $file Datei, in die gespeichert werden soll
 * @version 1.0
 */
function saveUser($file)
{
 global $user;
 $temp = fopen($file, 'w');
 fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . implode("\n", $user));
 fclose($temp);
}

/**
 * Überprüft, ob eine Kategorie mit dem Namen $name schon existiert.
 * 
 * @param string $name Name der zu suchenden Kategorie
 * @return int|bool Position im $cats-Array im Erfolgsfall, ansonsten false
 * @version 1.0.2
 */
function unifyCat($name)
{
 global $cats;
 foreach(array_slice($cats, 1) as $key => $value)
 {
  $value = explode("\t", $value);
  if($value[1] == $name) return $key+1;
 }
 return false;
}

/**
 * Überprüft, ob ein Smilie mit dem Synonym $synonym schon existiert.
 * 
 * @param string $synonym Synonym des zu suchenden Smilie
 * @return int|bool Position im $smilies-Array im Erfolgsfall, ansonsten false
 * @version 1.0.2
 */
function unifySmilie($synonym)
{
 global $smilies;
 foreach(array_slice($smilies, 1) as $key => $value)
 {
  $value = explode("\t", $value);
  if($value[1] == $synonym) return $key+1;
 }
 return false;
}

/**
 * Parst eine Sprachkonfigurationsdatei und erstellt pro Sektion eine PHP Datei mit den Sprachvariablen als Array.
 * 
 * @param string $inifile INI Datei
 * @version 1.0.2
 */
function parseLanguage($inifile)
{
 foreach(parse_ini_file($inifile, true) as $sec => $values)
 {
  $toWrite = "<?php\n";
  foreach($values as $key => $value) $toWrite .= "\$lang['$sec']['$key'] = '$value';\n";
  $temp = fopen('language_' . $sec . '.php', 'w');
  fwrite($temp, $toWrite . '?>');
  fclose($temp);
 }
}

/**
 * Speichert die derzeitigen News in $newsdat.
 * 
 * @version 1.0
 */
function saveNews()
{
 global $newsdat, $news;
 $temp = fopen($newsdat, 'w');
 flock($temp, LOCK_EX);
 fwrite($temp, implode("\n", $news));
 flock($temp, LOCK_UN);
 fclose($temp);
}

/**
 * Zeigt benötigte JavaScripts an.
 * 
 * @version 1.0.3.5
 */
function showJS()
{
 global $lang;
?>

<script type="text/javascript">
var sources = new Array('<?=implode('\', \'', $_POST['srcarray'])?>');
var activeNewsbox = 'newsbox';
var openendTags = new Object();

function doSource(add)
{
 var srcs = document.getElementById('sources');
 if(add)
 {
  if((prefix = srcs.value.substr(0, 4)) == 'http' || prefix == 'ftp:')
  {
   sources.push(srcs.value);
   srcs.value = '';
  }
  else alert('<?=$lang['news']['startsrc']?>');
 }
 else alert(sources.length > 1 ? '<?=$lang['news']['source']?> "' + sources.pop() + '" <?=$lang['news']['delsrc']?>' : '<?=$lang['news']['nosrc']?>');
 srcs.focus();
}

function addSource()
{
 document.getElementById('srcarray').value = sources.join('\t');
}

function toggleFullStory()
{
 if((nb2 = document.getElementById('newsbox2')).style.display == 'none')
 {
  document.getElementById('toggler').value = '<?=$lang['news']['discard']?> \u00AB';
  nb2.style.display = 'inline';
 }
 else
 {
  nb2.style.display = 'none';
  nb2.value = '';
  document.getElementById('toggler').value = '<?=$lang['news']['expand']?> \u00BB';
 }
}

function setNewsTag(openingTag, closingTag)
{
 var newsBox = document.getElementById(activeNewsbox);
 newsBox.focus();
//Inspired by http://aktuell.de.selfhtml.org/artikel/javascript/bbcode/
 if(typeof document.selection != 'undefined') //IE
 {
  var range = document.selection.createRange();
  var selectedText = range.text;
  range.text = openingTag + selectedText + closingTag;
  range = document.selection.createRange();
  selectedText.length == 0 ? range.move('character', -closingTag.length) : range.findText(selectedText);
  range.select();  
 }
 else if(typeof newsBox.selectionStart != 'undefined') //Gecko
 {
  var start = newsBox.selectionStart;
  var end = newsBox.selectionEnd;
  var selectedText = newsBox.value.substring(start, end);
  newsBox.value = newsBox.value.substr(0, start) + openingTag + selectedText + closingTag + newsBox.value.substr(end);
  if(selectedText.length == 0) newsBox.selectionStart = newsBox.selectionEnd = start + openingTag.length;
  else
  {
   newsBox.selectionStart = start + openingTag.length;
   newsBox.selectionEnd = start + openingTag.length + selectedText.length;
  }
 }
 else //Other
 {
  if(typeof openendTags[openingTag] == 'undefined')
  {
   newsBox.value += openingTag;
   openendTags[openingTag] = true;
  }
  else
  {
   newsBox.value += closingTag;
   delete(openendTags[openingTag]);
  }
 }
}
</script>

<?php
}

/*function moveFolder($oldname, $newname, $ext='*')
{
 if(rename($oldname, $newname)) return true;
 else
 {
  mkdir($newname, 0775);
  foreach(glob($oldname . '*.' . $ext) as $value) rename($value, $newname . $value);
  rmdir($oldname);
 }
}*/

/**
 * Gibt die aktuelle Version des Newsscript zurück.
 * 
 * @return string Versionsnummer
 * @since 1.0.1
 * @version 1.0.3.1
 */
function getNewsVersion()
{
 return '1.0.3.5';
}

/**
 * Entfernt Rückstriche und wandelt die einfachen HTML Sonderzeichen in Entitäten um.
 * 
 * @param string $string,... Die Zeichenkette(n)
 * @return mixed Einzelne oder Array mit bearbeiteter Zeichenkette
 * @since 1.0.2
 * @version 1.0.2
 */
function stripEscape($string)
{
 return count($strings = func_get_args()) > 1 ? array_map(create_function('$string', 'return htmlspecialchars(stripslashes($string), ENT_QUOTES);'), $strings) : htmlspecialchars(stripslashes($string), ENT_QUOTES);
}
?>