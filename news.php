<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

#
###---AB GEHT'S!---###
#
 $action = (!$_POST['action']) ? $_GET['action'] : $_POST['action'];
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
  for ($i=0; $i<count($topics_file); $i++)
  {
   $thread = file("forum/foren/" . $_GET['foren_id'] . "-" . str_replace("\n", "", str_replace("\r\n", "", $topics_file[$i])) . ".xbb");
   $thread = explode("\t", $thread[0]);
   echo("<option value=\"" . trim($topics_file[$i]) . "\">" . $thread[1] . "</option>\n");
  }
  echo("</select></form>");
 }

#
###---ADMIN---###
#
 elseif ($action == "admin")
 {
  include("functions.php");
  if ($mode == "login")
  {
   if (!file_exists("dats/pw.dat"))
   {
    $temp = fopen("dats/pw.dat", "w");
    fwrite($temp, md5($_POST['pw']));
    fclose($temp);
    head("", "Chrissyx Homepage: Administration - News", "", "", "style.css", "", "");
    echo("Passwort gespeichert - bitte damit <a href=\"" . $_SERVER['PHP_SELF'] . "?action=admin\">einloggen!</a>");
    tail();
   }
   else
   {
    $pw = file("dats/pw.dat");
    if ((md5($_POST['pw']) == $pw[0]) or ($_POST['pw2'] == $pw[0]))
    {
     unset($pw);
     if ($_POST['news'])
     {
      $array = explode("\n", stripslashes($_POST['news']));
      $news = "\t";
      if ($_POST['red'] == "true") $news .= "<span class=\"red\">";
      $news .= "<span class=\"b\">" . trim($array[0]) . "</span><br />\n";
      if ($_POST['zitat'] == "true") $news .= "\t<span class=\"i\">" . trim(nl2br($array[1])) . "\n";
      else $news .= "\t" . trim(nl2br($array[1])) . "\n";
      for ($i=2; $i<=count($array); $i++) $news .= "\t" . trim(nl2br($array[$i])) . "\n";
      if ($_POST['zitat'] == "true") $news = "\t" . trim($news) . "</span>";
      if ($_POST['red'] == "true") $news = "\t" . trim($news) . "</span>";
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
      $msg = "News geaddet! Noch eine? Ansonsten posten!<br /><br />\n";
     }
     head("", "Chrissyx Homepage: Administration - News", "", "", "style.css", "", "");
     echo($msg);
     ?>

  <span class="u">CHS - News - Administration</span><br /><br />

  <form name="newsform" action="<?=$_SERVER['PHP_SELF']?>" method="post">
  News:<br />
  <input type="button" value="URL" onClick="url()"> <input type="button" value="IMG" onClick="img()"> <input type="button" value="URL+IMG" onClick="urlimg()"> <a href="javascript:smilie('forum/images/smilies/1.gif');"><img src="forum/images/smilies/1.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/2.gif');"><img src="forum/images/smilies/2.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/4.gif');"><img src="forum/images/smilies/4.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/5.gif');"><img src="forum/images/smilies/5.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/8.gif');"><img src="forum/images/smilies/8.gif" border="0"></a><br />
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
  <input type="hidden" name="pw2" value="<?php ($_POST['pw']) ? print(md5($_POST['pw'])) : print($_POST['pw2']); ?>">
  </form><br /><br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="restform">
  Datum: <input type="text" name="datum" value="<?=date("d.m.Y")?>"><br /><br />
  Updates:<br />
  <table>
   <tr>
    <td><textarea name="updates" cols="50" rows="5"></textarea></td>
    <td>
     <a href="javascript:document.restform.updates.value += '-Update in der C&amp;C-Sektion.\n'; document.restform.updates.focus();">-Update in der C&amp;C-Sektion.</a><br>
     <a href="javascript:document.restform.updates.value += '-Update in der Download-Sektion.\n'; document.restform.updates.focus();">-Update in der Download-Sektion.</a><br>
     <a href="javascript:document.restform.updates.value += '-Versionsupdate in der Download-Sektion.\n'; document.restform.updates.focus();">-Versionsupdate in der Download-Sektion.</a><br>
    </td>
    <td>
     <a href="javascript:document.restform.updates.value += '-Update in der \n'; document.restform.updates.focus();">-Update in der</a><br>
     <a href="javascript:document.restform.updates.value += '-Update in der Link-Sektion.\n'; document.restform.updates.focus();">-Update in der Link-Sektion.</a><br>
     <a href="javascript:document.restform.updates.value += '-Update in der Scripts-Sektion.\n'; document.restform.updates.focus();">-Update in der Scripts-Sektion.</a><br>
    </td>
   </tr>
  </table><br /><br />

  Internes:<br />
  <textarea name="intern" cols="50" rows="5"></textarea><br /><br />
  <input type="submit" value="News posten">
  <input type="hidden" name="action" value="post">
  </form>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <input type="submit" value="News editieren">
  <input type="hidden" name="action" value="edit">
  </form>

     <?php
     tail();
    }
    else die("Falsches Passwort!");
   }
  }
  else
  {
   head("", "Chrissyx Homepage: Administration - News - LogIn", "", "", "style.css", "", "");
   ?>

  CHS - News - LogIn<br />
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  Bitte Passwort angeben: <input type="password" name="pw"><br />
  <input type="submit" value="Einloggen">
  <input type="hidden" name="mode" value="login">
  <input type="hidden" name="action" value="admin">
  </form>

   <?php
   tail();
  }
 }

#
###---NEWS POSTEN---###
#
 elseif ($action == "post")
 {
  include("functions.php");
  $towrite = (!$_POST['datum']) ? "\t<div class=\"center\"><span class=\"b\"><a name=\"" . date("d.m.Y") . "\">" . date("d.m.Y") . "</a></span></div>\n" : "\t<div class=\"center\"><span class=\"b\"><a name=\"" . $_POST['datum'] . "\">" . $_POST['datum'] . "</a></span></div>\n";
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
   $towrite .= "\t<span class=\"red\">Intern:</span> " . trim(nl2br($array[0])) . "\n";
   for ($i=1; $i<=count($array); $i++) $towrite .= "\t" . trim(nl2br($array[$i])) . "\n";
   $towrite = "\t" . trim($towrite) . "<br /><br />\n";
  }
  head("", "Chrissyx Homepage: Administration - News", "", "", "style.css", "", "");
  echo("Diese News wird gepostet:<br /><hr>\n$towrite\n<br><hr><a href=\"http://" . $_SERVER['SERVER_NAME'] . "\">Zurück nach Home</a>");
  tail();
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
  include("functions.php");
  if (file_exists("news.dat"))
  {
   if ($mode == "save")
   {
    $temp = fopen("news.dat", "w");
    fwrite($temp, stripslashes($_POST['news']));
    fclose($temp);
    head("", "Chrissyx Homepage: Administration - News", "", "", "style.css", "", "");
    echo("News editiert! <a href=\"http://" . $_SERVER['SERVER_NAME'] . "\">Zurück nach Home</a>");
    tail();
   }
   else
   {
    $temp = fopen("news.dat", "r");
    $temp2 = fread($temp, filesize("news.dat"));
    fclose($temp);
    head("", "Chrissyx Homepage: Administration - News - Edit", "", "", "style.css", "", "");
    ?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="newsform">
  <input type="button" value="URL" onClick="url()"> <input type="button" value="IMG" onClick="img()"> <input type="button" value="URL+IMG" onClick="urlimg()"> <a href="javascript:smilie('forum/images/smilies/1.gif');"><img src="forum/images/smilies/1.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/2.gif');"><img src="forum/images/smilies/2.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/4.gif');"><img src="forum/images/smilies/4.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/5.gif');"><img src="forum/images/smilies/5.gif" border="0"></a> <a href="javascript:smilie('forum/images/smilies/8.gif');"><img src="forum/images/smilies/8.gif" border="0"></a><br />
  <textarea name="news" cols="100" rows="30"><?=stripslashes($temp2)?></textarea><br />
  <input type="submit" value="Posten"> <input type="button" value="Abbrechen" onClick="javascript:document.location.href='http://<?=$_SERVER['SERVER_NAME']?>';">
  <input type="hidden" name="action" value="edit">
  <input type="hidden" name="mode" value="save">
  </form>

   <?php
   tail();
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
   echo("$temp2\n  <div class=\"center\"><a href=\"archiv2005.php\">Hier geht's zum Newsarchiv!</a></div>");
  }
  else echo("Keine News gefunden!");
 }
?>