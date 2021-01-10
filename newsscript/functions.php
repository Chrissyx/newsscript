<?php
//$action laden
$action = (!$_GET['action']) ? $_POST['action'] : $_GET['action'];

//echo Kurzform aktivieren
if(ini_get(short_open_tag) == '0') ini_set(short_open_tag, '1');

//Session laden, IP sichern
session_start();
if(!$_SESSION['session_ip']) $_SESSION['session_ip'] = $_SERVER['REMOTE_ADDR'];
else if($_SESSION['session_ip'] != $_SERVER['REMOTE_ADDR']) die('Nicht erlaubt, diese Session zu verwenden!');

//Aufbauzeit [PHP4]
$_SESSION['microtime'] = explode(' ', microtime());
$_SESSION['microtime'] = $_SESSION['microtime'][1] + $_SESSION['microtime'][0];

//Funktionen
/**
 * Generiert den XHTML Head für jede Seite und sendet den passenden Content-Type, wenn der Browser XML unterstützt.
 * 
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string Der Titel des Dokuments
 * @param string Metatag für Schlüsselwörter
 * @param string Metatag für Beschreibung
 * @param string Verwendeter Zeichensatz bzw. Kodierung
 * @param string Sprachkürzel
 * @param string Zusätzliche Angaben zum <html> Tag, mit Leerzeichen beginnen!
 * @param string Die zu benutzende CSS Datei
 * @param string Weitere optionale XHTML Tags im Head
 * @param string Zusätzliche optionale Angaben zum <body> Tag, mit Leerzeichen beginnen!
 * @see newsTail()
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
 * @author Chrissyx
 * @copyright Chrissyx
 * @see newsHead()
 */
function newsTail()
{
 $temp = explode(' ', microtime());
 $temp = ($temp[1] + $temp[0]) - $_SESSION['microtime'];
 echo('<div class="center" style="width:99%; clear:both;">');
 newsFont(1);
 echo('Seitenaufbauzeit: ' . round($temp, 4) . ' Sekunden</span></div>
 </body>
</html>');
}

/**
 * Gibt das CSS-Äquivalent zur HTML Schriftgröße aus. Nicht vergessen: </span>!
 * 
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string HTML Schriftgröße von 1 bis 7 oder eigener Wert.
 */
function newsFont($wert)
{
 switch($wert)
 {
  case 7:
  echo('<span style="font-size:300%;">');
  break;

  case 6:
  echo('<span style="font-size:xx-large;">');
  break;

  case 5:
  echo('<span style="font-size:x-large;">');
  break;

  case 4:
  echo('<span style="font-size:large;">');
  break;

  case 3:
  echo('<span style="font-size:medium;">');
  break;

  case 2:
  echo('<span style="font-size:small;">');
  break;

  case 1.5:
  echo('<span style="font-size:x-small;">');
  break;

  case 1:
  echo('<span style="font-size:xx-small;">');
  break;

  default:
  echo('<span style="font-size:' . $wert . ';">');
  break;
 }
}                      

/**
 * Überprüft, ob ein Benutzer mit dem Namen $name schon existiert.
 * 
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string Name des zu suchenden Nutzers
 * @return int|bool Position im $user-Array im Erfolgsfall, ansonsten false
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
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string Name des Nutzers
 * @return mixed Array mit Daten, ansonsten false
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
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string Datei, in die gespeichert werden soll
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
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string Name der zu suchenden Kategorie
 * @return int|bool Position im $cats-Array im Erfolgsfall, ansonsten false
 */
function unifyCat($name)
{
 global $cats;
 foreach($cats as $key => $value)
 {
  $value = explode("\t", $value);
  if($value[1] == $name) return $key;
 }
 return false;
}

/**
 * Überprüft, ob ein Smilie mit dem Synonym $synonym schon existiert.
 * 
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string Synonym des zu suchenden Smilie
 * @return int|bool Position im $smilies-Array im Erfolgsfall, ansonsten false
 */
function unifySmilie($synonym)
{
 global $smilies;
 foreach($smilies as $key => $value)
 {
  $value = explode("\t", $value);
  if($value[1] == $synonym) return $key;
 }
 return false;
}

/**
 * Parst eine Sprachkonfigurationsdatei und erstellt pro Sektion eine PHP Datei mit den Sprachvariablen als Array.
 * 
 * @author Chrissyx
 * @copyright Chrissyx
 * @param string INI Datei
 */
function parseLanguage($inifile)
{
 foreach(parse_ini_file($inifile, true) as $sec => $values)
 {
  $towrite = "<?php\n";
  $temp = fopen('language_' . $sec . '.php', 'w');
  foreach($values as $key => $value) $towrite .= "\$lang['$sec']['$key'] = '$value';\n";
  fwrite($temp, $towrite . '?>');
  fclose($temp);
 }
}

/**
 * Speichert die derzeitigen News in $newsdat.
 * 
 * @author Chrissyx
 * @copyright Chrissyx
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
 * @author Chrissyx
 * @copyright Chrissyx
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
 if(add)
 {
  if(document.getElementById('sources').value.substr(0, 7) == 'http://')
  {
   sources.push(document.getElementById('sources').value);
   document.getElementById('sources').value = '';
  }
  else alert('<?=$lang['news']['startsrc']?>');
 }
 else alert((sources.length > 1) ? '<?=$lang['news']['source']?> "' + sources.pop() + '" <?=$lang['news']['delsrc']?>' : '<?=$lang['news']['nosrc']?>');
 document.getElementById('sources').focus();
}

function addSource()
{
 document.getElementById('srcarray').value = sources.join('\t');
}

function setNewsSmilie(smilie)
{
 document.getElementById(activeNewsbox).value += smilie;
 document.getElementById(activeNewsbox).focus();
}

function toggleFullStory()
{
 if(document.getElementById('newsbox2').style.display == 'none')
 {
  document.getElementById('toggler').value = '<?=$lang['news']['discard']?> «';
  document.getElementById('newsbox2').style.display = 'inline';
 }
 else
 {
  document.getElementById('newsbox2').style.display = 'none';
  document.getElementById('newsbox2').value = '';
  document.getElementById('toggler').value = '<?=$lang['news']['expand']?> »';
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
  (selectedText.length == 0) ? range.move('character', -closingTag.length) : range.findText(selectedText);
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
/*
function moveFolder($oldname, $newname, $ext='*')
{
 if(rename($oldname, $newname)) return true;
 else
 {
  mkdir($newname, 0775);
  foreach(glob($oldname . '*.' . $ext) as $value) rename($value, $newname . $value);
  rmdir($oldname);
 }
}*/
?>