<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

#
###---AB GEHT'S!---###
#
 $action = $_POST['action'];
 if (!$action) $action = $_GET['action'];
 $mode = $_POST['mode'];

#
###---THREADING---###
#
 if ($action == "threading")
 {
  $topics_file = file("forum/foren/" . $_GET['foren_id'] . "-threads.xbb");
  $topics_file_size = sizeof($topics_file);
  $topics_file = array_reverse($topics_file);
  echo("<form name=\"thisform\"><select name=\"threadchoice\" size=\"" . $topics_file_size . "\" onClick=\"opener.document.newsform.thread_id.value=document.thisform.threadchoice.options[document.thisform.threadchoice.options.selectedIndex].value; window.close();\">\n");
  for ($i=0; $i<=count($topics_file); $i++)
  {
//   $thread = str_replace("\n", "", str_replace("\r\n", "", $topics_file[$i]));
   $thread = file("forum/foren/" . $_GET['foren_id'] . "-" . str_replace("\n", "", str_replace("\r\n", "", $topics_file[$i])) . ".xbb");
   $thread = explode("\t", $thread[0]);
//   $thread = $thread[1];
   echo("<option value=\"" . $topics_file[$i] . "\">" . $thread[1] . "</option>\n");
  }
  echo("</select></form>");
 }

#
###---ADMIN---###
#
 elseif ($action == "admin")
 {
  if ($mode == "login")
  {
   if (!file_exists("pw.dat"))
   {
    $temp = fopen("pw.dat", "w");
    fwrite($temp, md5($_POST['pw']));
    fclose($temp);
    echo("Passwort gespeichert - bitte damit <a href=\"news.php?action=admin\">einloggen!</a>");
   }
   else
   {
    $pw = file("pw.dat");
    if ((md5($_POST['pw']) == $pw[0]) or ($_POST['pw2'] == $pw[0]))
    {
     unset($pw);
     if ($_POST['news'])
     {
      $array = explode("\n", stripslashes($_POST['news']));
      $news = "\t";
      if ($_POST['red'] == "true") $news .= "<font color=\"red\">";
      $news .= "<b>" . trim($array[0]) . "</b><br />\n";
      if ($_POST['zitat'] == "true") $news .= "\t<i>" . trim(nl2br($array[1])) . "\n";
      else $news .= "\t" . trim(nl2br($array[1])) . "\n";
      for ($i=2; $i<=count($array); $i++) $news .= "\t" . trim(nl2br($array[$i])) . "\n";
      if ($_POST['zitat'] == "true") $news = "\t" . trim($news) . "</i>";
      if ($_POST['red'] == "true") $news = "\t" . trim($news) . "</font>";
      $news = "\t" . trim($news) . "<br />";
      if ($_POST['foren_id'] and $_POST['thread_id']) $news .= "\n\t<a href=\"forum/index.php?mode=viewthread&forum_id=" . $_POST['foren_id'] . "&thread=" . $_POST['thread_id'] . "\">Link zum Thema im Forum</a><br />";
      $news .= "<br />\n";
      if (!file_exists("temp.dat"))
      {
       $temp = fopen("temp.dat", "w");
       fwrite($temp, $news);
       fclose($temp);
      }
      else
      {
       $temp = fopen("temp.dat", "r");
       $temp2 = fread($temp, filesize("temp.dat"));
       fclose($temp);
       $temp2 .= $news;
       $temp = fopen("temp.dat", "w");
       fwrite($temp, $temp2);
       fclose($temp);
      }
      echo("News gedaddet! Noch eine? Ansonsten posten!<br /><br />");
     }
     ?>

<script language="JavaScript">
function url()
{
 text1 = prompt('URL eingeben', 'http://');
 if(text1 != null)
 {
  text2 = prompt('Text/URL eingeben, der den Link darstellen soll', text1);
  if(text2 != null) document.newsform.news.value += '<a href="'+text1+'">'+text2+'</a>';
 }
 document.newsform.news.focus();
}
function img()
{
 text = prompt('Bild URL eingeben:', 'http://');
 if(text != null) document.newsform.news.value += '<img src="'+text+'">';
 document.newsform.news.focus();
}
function urlimg()
{
 text1 = prompt('URL eingeben:', 'http://');
 if (text1 != null)
 {
  text2 = prompt('Bild URL eingeben:', text1);
  if (text2 != null) document.newsform.news.value += '<a href="'+text1+'"><img src="'+text2+'" border="0"></a>';
  document.newsform.news.focus();
 }
}
function smilie(smilie)
{
 document.newsform.news.value += '<img src="' + smilie + '">';
 document.newsform.news.focus();
}
function threading(id)
{
 url = 'news.php?action=threading&foren_id=' + id;
 window.open(url, 'Threading', 'scrollbars=yes, resizable=yes');
}
</script>

CHS - News - Administration<br /><br />

<form name="newsform" action="news.php" method="post">
News:<br />
<input type="button" value="URL" onClick="url()"> <input type="button" value="IMG" onClick="img()"> <input type="button" value="URL+IMG" onClick="urlimg()"> <a href="javascript:smilie('forum/images/smilies/1.gif')"><img src="forum/images/smilies/1.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/2.gif')"><img src="forum/images/smilies/2.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/4.gif')"><img src="forum/images/smilies/4.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/5.gif')"><img src="forum/images/smilies/5.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/8.gif')"><img src="forum/images/smilies/8.gif" border="0"></a><br />
<textarea name="news" cols="50" rows="10"></textarea><br />
Link zum Forumthema: <select name="foren_id" onChange="threading(this.form.foren_id.options[this.form.foren_id.selectedIndex].value);">
<option value="">Bitte auswählen...</option>
<?php
$foren = file("forum/vars/foren.var");
$foren_anzahl = sizeof($foren);
$kg = file("forum/vars/kg.var");
$kg_anzahl = sizeof($kg);
for ($j=0; $j<$kg_anzahl; $j++)
{
 $ak_kg = explode("\t", $kg[$j]);
 echo "<option value=\"\">--" . $ak_kg[1] . "</option>\n";
 for ($i=0; $i<$foren_anzahl; $i++)
 {
  $ak_forum = explode("\t", $foren[$i]);
  if ($ak_forum[5] == $ak_kg[0] && $ak_forum[0] != $forum_id) echo "<option value=\"" . $ak_forum[0] . "\">" . $ak_forum[1] . "</option>\n";
 }
 echo "<option value=\"\"></option>\n";
}
?>
</select> <input type="text" name="thread_id" size="3"><br />
<input type="checkbox" name="zitat" value="true">Zitat?
<input type="checkbox" name="red" value="true">Wichtig?
<input type="submit" value="News adden!">
<input type="hidden" name="action" value="admin">
<input type="hidden" name="mode" value="login">
<input type="hidden" name="pw2" value="<?php if ($_POST['pw']) echo(md5($_POST['pw'])); else echo($_POST['pw2']); ?>">
</form><br /><br />

<form action="news.php" method="post">
Datum: <input type="text" name="datum" value="<?php echo(date("d.m.Y")); ?>"><br /><br />
Updates:<br />
<textarea name="updates" cols="50" rows="5">-Update in der</textarea><br /><br />

Internes:<br />
<textarea name="intern" cols="50" rows="5"></textarea><br /><br />
<input type="submit" value="News posten">
<input type="hidden" name="action" value="post">
</form>

<form action="news.php" method="post">
<input type="submit" value="News editieren">
<input type="hidden" name="action" value="edit">
</form>

     <?php
    }
    else die("Falsches Passwort!");
   }
  }
  else
  {
   ?>

<form action="news.php" method="post">
PW: <input type="password" name="pw"><br />
<input type="submit" value="Einloggen">
<input type="hidden" name="mode" value="login">
<input type="hidden" name="action" value="admin">
</form>

   <?php
  }
 }

#
###---NEWS POSTEN---###
#
 elseif ($action == "post")
 {
  if (!$_POST['datum']) $towrite = "\t<center><b>" . date("d.m.Y") . "</b></center>\n";
  else $towrite = "\t<center><b>" . $_POST['datum'] . "</b></center>\n";
  if ($_POST['updates'])
  {
   $array = explode("\n", $_POST['updates']);
   for ($i=0; $i<=count($array); $i++) $towrite .= "\t" . trim(nl2br($array[$i])) . "\n";
   $towrite = "\t" . trim($towrite) . "<br /><br />\n";
  }
  if (file_exists("temp.dat"))
  {
   $temp = fopen("temp.dat", "r");
   $news = fread($temp, filesize("temp.dat"));
   fclose($temp);
   $towrite .= $news;
   unlink("temp.dat");
  }
  if ($_POST['intern'])
  {
   $array = explode("\n", $_POST['intern']);
   $towrite .= "\t<font color=\"red\">Intern:</font> " . trim(nl2br($array[0])) . "\n";
   for ($i=1; $i<=count($array); $i++) $towrite .= "\t" . trim(nl2br($array[$i])) . "\n";
   $towrite = "\t" . trim($towrite) . "<br /><br />\n";
  }
  echo("Diese News wurde gepostet:<hr>" . $towrite . "<hr><a href=\"index2.php\">Zurück nach Home</a>");
  if (file_exists("news.dat"))
  {
   $temp = fopen("news.dat", "r");
   $temp2 = fread($temp, filesize("news.dat"));
   fclose($temp);
   $towrite .= $temp2;
   $temp = fopen("news.dat", "w");
   fwrite($temp, $towrite);
   fclose($temp);
  }
  else
  {
   $temp = fopen("news.dat", "w");
   fwrite($temp, $towrite);
   fclose($temp);
  }
 }

#
###---NEWS EDIT---###
#
 elseif ($action == "edit")
 {
  if (file_exists("news.dat"))
  {
   if ($mode == "save")
   {
    $temp = fopen("news.dat", "w");
    fwrite($temp, stripslashes($_POST['news']));
    fclose($temp);
    echo("News editiert! <a href=\"index2.php\">Zurück nach Home</a>");
   }
   else
   {
    $temp = fopen("news.dat", "r");
    $temp2 = fread($temp, filesize("news.dat"));
    fclose($temp);
    ?>

<script language="JavaScript">
function url()
{
 text1 = prompt('URL eingeben', 'http://');
 if(text1 != null)
 {
  text2 = prompt('Text/URL eingeben, der den Link darstellen soll', text1);
  if(text2 != null) document.newsform.news.value += '<a href="'+text1+'">'+text2+'</a>';
 }
 document.newsform.news.focus();
}
function img()
{
 text = prompt('Bild URL eingeben:', 'http://');
 if(text != null) document.newsform.news.value += '<img src="'+text+'">';
 document.newsform.news.focus();
}
function urlimg()
{
 text1 = prompt('URL eingeben:', 'http://');
 if (text1 != null)
 {
  text2 = prompt('Bild URL eingeben:', text1);
  if (text2 != null) document.newsform.news.value += '<a href="'+text1+'"><img src="'+text2+'" border="0"></a>';
  document.newsform.news.focus();
 }
}
function smilie(smilie)
{
 document.newsform.news.value += '<img src="' + smilie + '">';
 document.newsform.news.focus();
}
</script>

<form action="news.php" method="post" name="newsform">
<input type="button" value="URL" onClick="url()"> <input type="button" value="IMG" onClick="img()"> <input type="button" value="URL+IMG" onClick="urlimg()"> <a href="javascript:smilie('forum/images/smilies/1.gif')"><img src="forum/images/smilies/1.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/2.gif')"><img src="forum/images/smilies/2.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/4.gif')"><img src="forum/images/smilies/4.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/5.gif')"><img src="forum/images/smilies/5.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/8.gif')"><img src="forum/images/smilies/8.gif" border="0"></a><br />
<textarea name="news" cols="100" rows="30"><?php echo(stripslashes($temp2)); ?></textarea><br />
<input type="submit" value="Posten"> <input type="button" value="Abbrechen" onClick="javascript:document.location.href='index2.php';">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="mode" value="save">
</form>

   <?php
   }
  }
  else echo("Keine News gefunden!");
 }

#
###---NEWS ZEIGEN---###
#
 else
 {
  if (file_exists("news.dat"))
  {
   $temp = fopen("news.dat", "r");
   $temp2 = fread($temp, filesize("news.dat"));
   fclose($temp);
   echo($temp2);
   //echo("<a href=\"news.php?action=admin\">einloggen!</a>");
  }
  else echo("Keine News gefunden!");
 }
?>