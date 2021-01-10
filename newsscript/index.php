<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

if(!is_dir('../newsscript/')) die('<b>ERROR:</b> Konnte Verzeichnis &quot;newsscript&quot; nicht finden!');
elseif(!file_exists('../news.php')) die('<b>ERROR:</b> Konnte &quot;news.php&quot; nicht finden!');
elseif(!file_exists('style.css')) die('<b>ERROR:</b> Konnte &quot;style.css&quot; nicht finden!');
elseif(!file_exists('functions.php')) die('<b>ERROR:</b> Konnte &quot;functions.php&quot; nicht finden!');
else include('functions.php');

if(file_exists('settings.dat.php'))
{
 if(!$_SESSION['newspw'] || !$_SESSION['newsname'] || !$_SESSION['newsadmin'])
 {
  header('Location: ../news.php?action=admin');
  exit();
 }
 else
 {
  $settings = array_map('trim', array_slice(file('settings.dat.php'), 1));
  $user = @array_map('trim', array_slice(file('../' . $settings[2]), 1)) or die('<b>ERROR:</b> Benutzer nicht gefunden!');
  $value = getUser($_SESSION['newsname']) or die('<b>ERROR:</b> Admin nicht gefunden!');
  if($_SESSION['dispall'] || $value[2] != $_SESSION['newsadmin'] || $value[1] != $_SESSION['newspw']) die('<b>ERROR:</b> Keine Adminrechte!');
  $action = 'admin';
 }
}
else
{
 $temp = basename($_SERVER['PHP_SELF']);
 if(decoct(fileperms($temp)) != '100775') chmod($temp, 0775) or die('<b>ERROR:</b> Konnte f�r &quot;' . $temp . '&quot; keine Rechte setzen!');
 elseif(decoct(fileperms('../news.php')) != '100775') chmod('../news.php', 0775) or die('<b>ERROR:</b> Konnte f�r &quot;news.php&quot; keine Rechte setzen!');
 elseif(decoct(fileperms('../newsscript/')) != '40775') chmod('../newsscript/', 0775) or die('<b>ERROR:</b> Konnte f�r den Ordner &quot;newsscript&quot; keine Rechte setzen!');
 clearstatcache();
}

if(!file_exists('language_index.php'))
{
 if($_GET['inifile']) parseLanguage($_GET['inifile']);
 else
 {
  newsHead('CHS - Newsscript: Choose language', 'Newsscript, CHS, choose, language, Chrissyx', 'Choose the language for the Newsscript from CHS', 'UTF-8', 'en');
  echo('  <div class="center" style="width:99%; border:1px solid #000000; padding:5px; margin-bottom:1%;">' . "\n" . '   <h3>Choose a language:</h3>' . "\n");
  foreach(glob('*.ini') as $value) echo('   <a href="' . $_SERVER['PHP_SELF'] . '?inifile=' . $value . '">' . $value . '</a><br />' . "\n");
  echo("  </div>\n  ");
  newsTail();
  exit();
 }
}

switch($action)
{
# Administration #
 case 'admin':
 include('language_index.php');
 newsHead('CHS - Newsscript: ' . $lang['index']['administration'], 'Newsscript, CHS, ' . $lang['index']['administration'] . ', Chrissyx', $lang['index']['administration'] . ' des Newsscript von CHS', $lang['index']['charset'], $lang['index']['code']);
 echo('  <h3>CHS - Newsscript: ' . $lang['index']['administration'] . '</h3>
  <div style="border:1px solid #000000; padding:5px; float:left;">
   <h4>' . $lang['index']['navigation'] . '</h4>
   <ul>
    <li><a href="' . $_SERVER['PHP_SELF'] . '">' . $lang['index']['homepage'] . '</a></li>
    <li><a href="' . $_SERVER['PHP_SELF'] . '?page=settings">' . $lang['index']['settings'] . '</a></li>
    <li><a href="' . $_SERVER['PHP_SELF'] . '?page=user">' . $lang['index']['user'] . '</a></li>
    <li><a href="' . $_SERVER['PHP_SELF'] . '?page=cats">' . $lang['index']['cats'] . '</a></li>
    <li><a href="' . $_SERVER['PHP_SELF'] . '?page=smilies">' . $lang['index']['smilies'] . '</a></li>
    <li><a href="' . $_SERVER['PHP_SELF'] . '?page=lang">' . $lang['index']['lang'] . '</a></li>
    <li><a href="' . $_SERVER['PHP_SELF'] . '?page=help">' . $lang['index']['help'] . '</a></li>
    <li><a href="' . (($settings[11]) ? $settings[11] : '../news.php') . '?action=newsout">' . $lang['index']['logout'] . ', ' . $_SESSION['newsname'] . '</a></li>
   </ul>
  </div>
  <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;">
');
 switch($_GET['page'])
 {

# Administration: Einstellungen #
  case 'settings':
  include('language_settings.php');
  if($_POST['update'])
  {
   $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</span><br /><br />\n";
   if(!$_POST['newsdat']) $settings[0] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['newsmax']) $settings[1] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['newspwsdat'] || (substr($_POST['newspwsdat'], -4) != '.php')) $settings[2] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['newscomments']) $settings[3] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['newscatsdat']) $settings[4] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['newscatpics']) $settings[5] .= '" style="border-color:#FF0000;';
   elseif($_POST['newssmilies'] && (substr($_POST['newssmilies'], -4) != '.var') && !$_POST['smiliepics']) $settings[7] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['tickermax']) $settings[10] .= '" style="border-color:#FF0000;';
   else
   {
    if($_POST['newsdat'] != $settings[0]) rename('../' . $settings[0], '../' . $_POST['newsdat']) or $_POST['newsdat'] = $settings[0];
    if($_POST['newspwsdat'] != $settings[2]) rename('../' . $settings[2], '../' . $_POST['newspwsdat']) or $_POST['newspwsdat'] = $settings[2];
    if($_POST['newscomments'] != $settings[3]) rename('../' . $settings[3], '../' . $_POST['newscomments']) or $_POST['newscomments'] = $settings[3]; #todo: verschieben in existierenden ordner?
    if($_POST['newscatsdat'] != $settings[4]) rename('../' . $settings[4], '../' . $_POST['newscatsdat']) or $_POST['newscatsdat'] = $settings[4];
    if($_POST['newscatpics'] != $settings[5]) rename('../' . $settings[5], '../' . $_POST['newscatpics']) or $_POST['newscatpics'] = $settings[5]; #todo: verschieben in existierenden ordner?
    //Drei F�lle: Keine smilies, smilies.var oder smilies.dat - Jeder Fall kann zu einen anderen werden.
    if($_POST['newssmilies'] != $settings[6])
    {
     if($_POST['newssmilies'] && (substr($_POST['newssmilies'], -4) != '.var')) //Neu oder Update .dat
     {
      if(!$settings[6] || (substr($settings[6], -4) == '.var')) //Neu .dat
      {
       $temp = fopen('../' . $_POST['newssmilies'], 'w');
       fwrite($temp, '0');
       fclose($temp);
       mkdir('../' . $_POST['smiliepics'], 0775);
      }
      else //Update .dat
      {
       rename('../' . $settings[6], '../' . $_POST['newssmilies']) or $_POST['newssmilies'] = $settings[6];
       rename('../' . $settings[7], '../' . $_POST['smiliepics']) or $_POST['smiliepics'] = $settings[7]; #todo: verschieben in existierenden ordner?
	  }
     }
     elseif((!$_POST['newssmilies'] || (substr($_POST['newssmilies'], -4) == '.var')) && $settings[6] && (substr($settings[6], -4) != '.var')) //Keine .dat
     {
      unlink('../' . $settings[6]);
      if (!@rmdir('../' . $settings[7]))
      {
       foreach(glob('../' . $settings[7] . '*.*') as $value) unlink($value);
       rmdir('../' . $settings[7]);
      }
      $_POST['smiliepics'] = ''; //Ordner muss auch weg
      unlink('smilies.php'); //Gecachete Smilies auch
     }
    }
    $temp = fopen('settings.dat.php', 'w');
    fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . $_POST['newsdat'] . "\n" . $_POST['newsmax'] . "\n" . $_POST['newspwsdat'] . "\n" . $_POST['newscomments'] . "\n" . $_POST['newscatsdat'] . "\n" . $_POST['newscatpics'] . "\n" . $_POST['newssmilies'] . "\n" . $_POST['smiliepics'] . "\n" . $_POST['smiliesmax']. "\n" . $_POST['smiliesmaxrow'] . "\n" . $_POST['tickermax'] . "\n" .  $_POST['redir']);
    fclose($temp);
    $settings = array_map('trim', array_slice(file('settings.dat.php'), 1));
    $temp = '   <span class="green">&raquo; ' . $lang['settings']['new'] . "</span><br /><br />\n";
   }
  }
  else unset($temp);
  ?>
  <h4><?=$lang['settings']['title']?></h4>
   <?=$lang['settings']['intro']?><br /><br />
<?=$temp?>   <form id="form" action="<?=$_SERVER['PHP_SELF']?>?page=settings" method="post">
   <table>
    <tr><td><?=$lang['settings']['numofnews']?></td><td><input type="text" name="newsmax" value="<?=$settings[1]?>" size="25" /></td></tr>
    <tr><td><?=$lang['settings']['locnews']?></td><td><input type="text" name="newsdat" value="<?=$settings[0]?>" size="25" /></td></tr>
    <tr><td><?=$lang['settings']['locpws']?></td><td><input type="text" name="newspwsdat" value="<?=$settings[2]?>" size="25" /></td></tr>
    <tr><td><?=$lang['settings']['foldcomments']?></td><td><input type="text" name="newscomments" value="<?=$settings[3]?>" size="25" /></td></tr>
    <tr><td><?=$lang['settings']['loccats']?></td><td><input type="text" name="newscatsdat" value="<?=$settings[4]?>" size="25" /></td></tr>
    <tr><td><?=$lang['settings']['foldpics']?></td><td><input type="text" name="newscatpics" value="<?=$settings[5]?>" size="25" /></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><?=$lang['settings']['locsmilies']?></td><td><input type="text" name="newssmilies" value="<?=$settings[6]?>" size="25" /></td></tr>
    <tr><td>(<?=$lang['settings']['foldsmilies']?></td><td><input type="text" name="smiliepics" value="<?=$settings[7]?>" size="25" />)</td></tr>
    <tr><td><?=$lang['settings']['numofsmilies']?></td><td><input type="text" name="smiliesmax" value="<?=$settings[8]?>" size="25" /></td></tr>
    <tr><td><?=$lang['settings']['rowofsmilies']?></td><td><input type="text" name="smiliesmaxrow" value="<?=$settings[9]?>" size="25" /></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><?=$lang['settings']['numofticks']?></td><td><input type="text" name="tickermax" value="<?=$settings[10]?>" size="25" /></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><?=$lang['settings']['redir']?></td><td><input type="text" name="redir" value="<?=$settings[11]?>" size="25" /></td></tr>
   </table>
   <br />
   <input type="submit" value="<?=$lang['index']['update']?>" /> <input type="reset" value="<?=$lang['index']['reset']?>" />
   <input type="hidden" name="update" value="true" />
   </form>
  <?php
  break;

# Administration: Benutzerverwaltung #
  case 'user':
  include('language_user.php');
  if($_POST['update'])
  {
   $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</span><br /><br />\n";
   if(!$_POST['name']) $_POST['name'] .= '" style="border-color:#FF0000;';
   elseif(!preg_match('/[\.0-9a-z_-]+@[\.0-9a-z-]+\.[a-z]+/si', $_POST['email'])) $_POST['email'] .= '" style="border-color:#FF0000;';
   elseif($_POST['user'] && $_POST['name']) //Vorhandener User
   {
    $key = unifyUser($_POST['user']);
    if($_POST['delete']) unset($user[$key]);
    else
    {
     $value = explode("\t", $user[$key]);
     $user[$key] = $_POST['name'] . "\t" . $value[1] . "\t" . (($_POST['isadmin'] == 'on') ? '1' :'0') . "\t" . $_POST['email'] . (($value[4]) ? "\t" . $value[4] : '');
	}
	saveUser('../' . $settings[2]);
	unset($_POST['name'], $_POST['email']);
    $temp = '   <span class="green">&raquo; ' . $lang['user']['edit'] . "</span><br /><br />\n";
   }
   elseif($_POST['name'] && (unifyUser($_POST['name']) === false)) //Neuer User
   {
    for($i=0; $i<10; $i++) $newpw .= chr(mt_rand(33, 126));
    $user[] = $_POST['name'] . "\t" . md5($newpw) . "\t" . (($_POST['isadmin'] == 'on') ? '1' :'0') . "\t" . $_POST['email'];
    $temp = fopen('../' . $settings[2], 'a');
    fwrite($temp, "\n" . end($user));
    fclose($temp);
    $temp = '   <span class="green">&raquo; ' . $lang['user']['new'] . ((mail($_POST['email'], $_SERVER['SERVER_NAME'] . ' Newsscript: ' . $lang['user']['subject'], sprintf($lang['user']['text'], $_POST['name'], $_SERVER['SERVER_NAME'], (($_POST['isadmin'] == 'on') ? $lang['user']['admin'] : $lang['user']['poster']), $newpw), 'From: newsscript@' . $_SERVER['SERVER_NAME'] . "\n" . 'Reply-To: ' . $_POST['email'] . "\n" . 'X-Mailer: PHP/' . phpversion() . "\n" . 'Content-Type: text/plain; charset=' . $lang['index']['charset'])) ? ' ' . $lang['user']['send'] : '</span> <span style="color:#FF0000; font-weight:bold;">' . $lang['user']['nosend']) . "</span><br /><br />\n"; #\r\n ???
    unset($newpw, $_POST['name'], $_POST['email']);
   }
   else $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . sprintf($lang['user']['exist'], $_POST['name']) . "</span><br /><br />\n";
  }
  else unset($temp);
  echo("\n" . '   <script type="text/javascript">' . "\n");
  $temp2 = '   var user = new Array(';
  foreach($user as $key => $value)
  {
   $value = explode("\t", $value);
   $temp2 .= 'new Array(\'' . $value[0] . '\', ' . $value[2] . ', \'' . $value[3] . '\'), ';
  }
  echo($temp2 . "'Windows 98SE rulez');\n");
  ?>

   function fillForm(key)
   {
    document.getElementById('name').value = user[key][0];
    document.getElementById('isadmin').checked = (user[key][1] == 1) ? true : false;
    document.getElementById('email').value = user[key][2];
    document.getElementById('delete').disabled = false;
    document.getElementById('user').value = user[key][0];
   };
   </script>

   <h4><?=$lang['user']['title']?></h4>
   <?=$lang['user']['intro']?><br /><br />
<?=$temp?>   <form action="<?=$_SERVER['PHP_SELF']?>?page=user" method="post">
   <table style="float:left;">
    <tr><td><?=$lang['user']['name']?></td><td><input type="text" name="name" id="name" value="<?=$_POST['name']?>" size="25" /></td></tr>
    <tr><td><?=$lang['user']['email']?></td><td><input type="text" name="email" id="email" value="<?=$_POST['email']?>" size="25" /></td></tr>
    <tr><td><?=$lang['user']['isadmin']?></td><td><input type="checkbox" name="isadmin" id="isadmin" /></td></tr>
    <tr><td><?=$lang['user']['delete']?></td><td><span style="background-color:#FF0000;"><input type="checkbox" name="delete" id="delete" disabled="disabled" /></span></td></tr>
   </table>
   <div style="border:1px solid #000000; margin-left:10px; padding:5px; float:left;">
    <?=$lang['user']['change']?><br />
<?php
foreach($user as $key => $value)
{
 $value = explode("\t", $value);
 echo('    <input type="radio" name="users" onclick="fillForm(' . $key . ');" />' . $value[0] . "<br />\n");
}
?>   </div>
   <br style="clear:both;" /><br />
   <!-- delete warnung -->
   <input type="submit" value="<?=$lang['index']['update']?>" /> <input type="reset" value="<?=$lang['index']['reset']?>" onmouseup="document.getElementById('delete').disabled=true; document.getElementById('user').value='';" />
   <input type="hidden" name="update" value="true" />
   <input type="hidden" name="user" id="user" />
   </form>
  <?php
  break;

# Administration: Kategorien #
  case 'cats':
  include('language_cats.php');
  $cats = array_map('trim', file('../' . $settings[4]));
  if($_POST['update'])
  {
   $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</span><br /><br />\n";
   if(!$_POST['catname']) $_POST['catname'] .= '" style="border-color:#FF0000;';
   elseif($_FILES['uploadpic']['name'] && !preg_match("/(.*)\.(jpg|jpeg|gif|png|bmp)/i", $_FILES['uploadpic']['name'])) $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
   else
   {
    switch($_FILES['uploadpic']['error'])
    {
     case 0: //Mit Upload
     if(move_uploaded_file($_FILES['uploadpic']['tmp_name'], '../' . $settings[5] . $_FILES['uploadpic']['name']))
     {
      chmod('../' . $settings[5] . $_FILES['uploadpic']['name'], 0775);
      $_POST['catpic'] = /*current(array_slice(explode('/', $settings[5]), -2)) . '/' .*/ $_FILES['uploadpic']['name']; 
	 }
     else
     {
      $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picprocess'] . "</span><br /><br />\n";
      break;
	 }

     case 4: //Kein Upload
     if($_POST['cat'] && $_POST['catname']) //Vorhandene Kategorie
     {
      $key = unifyCat($_POST['cat']);
      $value = explode("\t", $cats[$key]);
      if($_POST['delete'])
      {
       if($value[2] && file_exists('../' . $settings[5] . $value[2])) unlink('../' . $settings[5] . $value[2]);
       unset($cats[$key]);
	  }
      else
      {
       if(($_POST['catpic'] != $value[2]) && $value[2] && file_exists('../' . $settings[5] . $value[2])) unlink('../' . $settings[5] . $value[2]);
       $cats[$key] = $value[0] . "\t" . $_POST['catname'] . "\t" . $_POST['catpic'];
      }
      $temp = fopen('../' . $settings[4], 'w');
      fwrite($temp, implode("\n", $cats));
      fclose($temp);
	  unset($_POST['catname'], $_POST['catpic']);
      $temp = '   <span class="green">&raquo; ' . $lang['cats']['edit'] . "</span><br /><br />\n";
     }
     elseif($_POST['catname'] && !unifyCat($_POST['catname'])) //Neue Kategorie
     {
      $cats[] = $cats[0]++ . "\t" . htmlspecialchars($_POST['catname']) . "\t" . $_POST['catpic'];
      $temp = fopen('../' . $settings[4], 'w');
      fwrite($temp, implode("\n", $cats));
      fclose($temp);
	  unset($_POST['catname'], $_POST['catpic']);
	  $temp = '   <span class="green">&raquo; ' . $lang['cats']['new'] . "</span><br /><br />\n";
     }
     else $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . sprintf($lang['cats']['exist'], $_POST['catname']) . "</span><br /><br />\n";
     break;

     case 3:
     $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picpartial'] . "</span><br /><br />\n";
     $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
     break;

     case 2:
     case 1:
     $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picbigsize'] . "</span><br /><br />\n";
     $_FILES['uploadpic']['name'].= '" style="border-color:#FF0000;';
     break;

     default:
     $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picunknown'] . "</span><br /><br />\n";
     break;
    }
   }
  }
  else unset($temp);
  array_shift($cats); //Last CatID raus
  echo("\n" . '   <script type="text/javascript">' . "\n");
  $temp2 = '   var cats = new Array(';
  foreach($cats as $key => $value)
  {
   $value = explode("\t", $value);
   $temp2 .= 'new Array(\'' . $value[1] . '\', \'' . $value[2] . '\'), ';
  }
  echo($temp2 . "'Windows 98SE rulez');\n");
  ?>

   function fillForm(key)
   {
    document.getElementById('catname').value = cats[key][0];
    document.getElementById('catpic').value = cats[key][1];
    document.getElementById('pic').src = (cats[key][1] == '') ? 'frage.jpg' : ((cats[key][1].indexOf('/') == -1) ? '<?='../'.$settings[5]?>' : ((cats[key][1].substr(0, 3) == '../') ? '../' : '')) + cats[key][1];
    document.getElementById('delete').disabled = false;
    document.getElementById('cat').value = cats[key][0];
   };
   </script>

   <h4><?=$lang['cats']['title']?></h4>
   <?=$lang['cats']['intro']?><br /><br />
<?=$temp?>   <form action="<?=$_SERVER['PHP_SELF']?>?page=cats" method="post" enctype="multipart/form-data">
   <table style="float:left;">
    <tr><td><?=$lang['cats']['name']?></td><td><input type="text" name="catname" id="catname" value="<?=$_POST['catname']?>" size="45" /></td><td rowspan="5"><img src="frage.jpg" alt="CatPic" id="pic" /></td></tr>
    <tr><td><?=$lang['cats']['pic']?></td><td><input type="text" name="catpic" id="catpic" value="<?=$_POST['catpic']?>" size="45" /></td></tr>
    <tr><td colspan="2"><?=$lang['cats']['hint1']?></td></tr>
    <tr><td><?=$lang['index']['upload']?></td><td><input type="file" name="uploadpic" value="<?=$_FILES['uploadpic']['name']?>" size="25" /></td></tr>
    <tr><td><?=$lang['cats']['delete']?></td><td><span style="background-color:#FF0000;"><input type="checkbox" name="delete" id="delete" disabled="disabled" /></span></td></tr>
   </table>
   <div style="border:1px solid #000000; margin-left:10px; padding:5px; float:left;">
    <?=$lang['cats']['change']?><br />
<?php
foreach($cats as $key => $value)
{
 $value = explode("\t", $value);
 echo('    <input type="radio" name="cats" onclick="fillForm(' . $key . ');" />' . $value[1] . "<br />\n");
}
?>   </div>
   <br style="clear:both;" />
   <?php newsFont(2); echo($lang['cats']['hint2']); ?></span><br /><br />
   <input type="submit" value="<?=$lang['index']['update']?>" /> <input type="reset" value="<?=$lang['index']['reset']?>" onmouseup="document.getElementById('delete').disabled=true; document.getElementById('pic').src='frage.jpg'; document.getElementById('cat').value='';" />
   <input type="hidden" name="update" value="true" />
   <input type="hidden" name="cat" id="cat" />
   </form>
  <?php
  break;

  case 'smilies':
  include('language_smilies.php');
  if(!$settings[6])
  {
   echo('&raquo; ' . $lang['smilies']['note1']);
   break;
  }
  elseif(substr($settings[6], -4) == '.var')
  {
   echo('&raquo; ' . $lang['smilies']['note2']);
   break;
  }
  else $smilies = array_map('trim', file('../' . $settings[6]));
  if($_POST['update'])
  {
   $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</span><br /><br />\n";
   if(!$_POST['synonym']) $_POST['synonym'] .= '" style="border-color:#FF0000;';
   elseif(!$_POST['address'] && !$_FILES['uploadpic']['name'])
   {
    $_POST['address'] .= '" style="border-color:#FF0000;';
    $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
   }
   elseif($_FILES['uploadpic']['name'] && !preg_match("/(.*)\.(jpg|jpeg|gif|png|bmp)/i", $_FILES['uploadpic']['name'])) $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
   else
   {
    switch($_FILES['uploadpic']['error'])
    {
     case 0: //Mit Upload
     if(move_uploaded_file($_FILES['uploadpic']['tmp_name'], '../' . $settings[7] . $_FILES['uploadpic']['name']))
     {
      chmod('../' . $settings[7] . $_FILES['uploadpic']['name'], 0775);
      $_POST['address'] = $_FILES['uploadpic']['name']; 
	 }
     else
     {
      $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picprocess'] . "</span><br /><br />\n";
      break;
	 }

     case 4: //Kein Upload
     if($_POST['smilie'] && $_POST['synonym']) //Vorhandener Smilie
     {
      $key = unifySmilie($_POST['smilie']);
      $value = explode("\t", $smilies[$key]);
      if($_POST['delete'])
      {
       if(file_exists('../' . $settings[7] . $value[2])) unlink('../' . $settings[7] . $value[2]);
       unset($smilies[$key]);
	  }
      else
      {
       if(($_POST['address'] != $value[2]) && $value[2] && file_exists('../' . $settings[7] . $value[2])) unlink('../' . $settings[7] . $value[2]);
       $smilies[$key] = $value[0] . "\t" . $_POST['synonym'] . "\t" . $_POST['address'];
      }
      $temp = fopen('../' . $settings[6], 'w');
      fwrite($temp, implode("\n", $smilies));
      fclose($temp);
	  unset($_POST['synonym'], $_POST['address']);
      $temp = '   <span class="green">&raquo; ' . $lang['smilies']['edit'] . "</span><br /><br />\n";
     }
     elseif($_POST['synonym'] && !unifySmilie($_POST['synonym'])) //Neuer Smilie
     {
      $smilies[] = $smilies[0]++ . "\t" . $_POST['synonym'] . "\t" . $_POST['address'];
      $temp = fopen('../' . $settings[6], 'w');
      fwrite($temp, implode("\n", $smilies));
      fclose($temp);
	  unset($_POST['synonym'], $_POST['address']);
	  $temp = '   <span class="green">&raquo; ' . $lang['smilies']['new'] . "</span><br /><br />\n";
     }
     else $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . sprintf($lang['smilies']['exist'], $_POST['synonym']) . "</span><br /><br />\n";
     break;

     case 3:
     $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picpartial'] . "</span><br /><br />\n";
     $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
     break;

     case 2:
     case 1:
     $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picbigsize'] . "</span><br /><br />\n";
     $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
     break;

     default:
     $temp = '   <span style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picunknown'] . "</span><br /><br />\n";
     break;
    }
   }
  }
  else unset($temp);
  array_shift($smilies); //Last SmilieID raus
  echo("\n" . '   <script type="text/javascript">' . "\n");
  $temp2 = '   var smilies = new Array(';
  foreach($smilies as $key => $value)
  {
   $value = explode("\t", $value);
   $temp2 .= 'new Array(\'' . $value[1] . '\', \'' . $value[2] . '\'), ';
  }
  echo($temp2 . "'Windows 98SE rulez');\n");
  ?>

   function fillForm(key)
   {
    document.getElementById('synonym').value = smilies[key][0];
    document.getElementById('address').value = smilies[key][1];
    document.getElementById('delete').disabled = false;
    document.getElementById('smilie').value = smilies[key][0];
   };
   </script>

   <h4><?=$lang['smilies']['title']?></h4>
   <?=$lang['smilies']['intro']?><br /><br />
<?=$temp?>   <form action="<?=$_SERVER['PHP_SELF']?>?page=smilies" method="post" enctype="multipart/form-data">
   <table style="float:left;">
    <tr><td><?=$lang['smilies']['synoym']?></td><td><input type="text" name="synonym" id="synonym" value="<?=$_POST['synonym']?>" size="45" /></td></tr>
    <tr><td><?=$lang['smilies']['adress']?></td><td><input type="text" name="address" id="address" value="<?=$_POST['address']?>" size="45" /></td></tr>
    <tr><td colspan="2"><?=$lang['smilies']['hint1']?></td></tr>
    <tr><td><?=$lang['index']['upload']?></td><td><input type="file" name="uploadpic" value="<?=$_FILES['uploadpic']['name']?>" size="25" /></td></tr>
    <tr><td><?=$lang['smilies']['delete']?></td><td><span style="background-color:#FF0000;"><input type="checkbox" name="delete" id="delete" disabled="disabled" /></span></td></tr>
   </table>
   <div style="border:1px solid #000000; margin-left:10px; padding:5px; float:left;">
    <?=$lang['smilies']['change']?><br />
<?php
$i=0;
foreach($smilies as $value)
{
 $value = explode("\t", $value);
 echo('    <img src="' . ((strpos($value[2], '/') === false) ? '../' . $settings[7] : ((substr($value[2], 0, 3) == '../') ? '../' : '')) . $value[2] . '" alt="' . $value[1] . '" style="cursor:pointer;" onclick="fillForm(' . $i++ . ');" />');
 if(($i % $settings[9]) == 0) echo("<br />\n");
}
?>   </div>
   <br style="clear:both;" />
   <?php newsFont(2); echo($lang['smilies']['hint2']); ?></span><br /><br />
   <input type="submit" value="<?=$lang['index']['update']?>" /> <input type="reset" value="<?=$lang['index']['reset']?>" onmouseup="document.getElementById('delete').disabled=true; document.getElementById('smilie').value='';" />
   <input type="hidden" name="update" value="true" />
   <input type="hidden" name="smilie" id="smilie" />
   </form>
  <?php
  break;

# Administration: Sprache �nden #
  case 'lang':
  include('language_lang.php');
  if($_POST['inifile'])
  {
   parseLanguage($_POST['inifile']);
   include('language_lang.php');
   $temp = '   <span class="green">&raquo; ' . $lang['lang']['new'] . "</span><br /><br />\n";
  }
  else unset($temp);
  echo('   <h4>' . $lang['lang']['title'] . '</h4>
' . $temp . '   <form action="' . $_SERVER['PHP_SELF'] . '?page=lang" method="post">
   ' . $lang['lang']['intro'] . ' <select name="inifile">
');
foreach(glob('*.ini') as $value) echo('    <option>' . $value . "</option>\n");
  echo('   </select><br /><br />
   <input type="submit" value="' . $lang['index']['update'] . '" />
   </form>
');
  break;

# Administration: Hilfe & Infos #
  case 'help':
  include('language_help.php');
  ?>
  <h4><?=$lang['help']['title']?></h4>
  <div style="padding-right:5px; float:left;">
   <?=$lang['help']['hint1']?><br />
   <a href="http://www.chrissyx.com/scripts.php" target="_blank">http://www.chrissyx.com/scripts.php</a><br /><br />
   <?=$lang['help']['hint2']?><br />
   <a href="http://www.chrissyx-forum.de.vu/" target="_blank">http://www.chrissyx-forum.de.vu/</a><br /><br />
   <a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" /></a> &oline; <a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank"><img src="http://jigsaw.w3.org/css-validator/images/vcss" alt="CSS ist valide!" /></a> &oline; <a href="http://www.validome.org/referer" target="_blank"><img src="http://www.validome.org/images/set2/valid_xhtml_1_0.gif" alt="Valid XHTML 1.0" /></a><?=(file_exists('../newsticker.php') ? ' &oline; <a href="http://feedvalidator.org/check.cgi?url=http://' . $_SERVER['SERVER_NAME'] . substr(dirname($_SERVER['PHP_SELF']), 0, strrpos(dirname($_SERVER['PHP_SELF']), '/')) . '/newsticker.php?type=rss" target="_blank"><img src="valid-rss.png" alt="[Valid RSS]" title="Validate my RSS feed" /></a>' : '')?>
  </div>
  <div style="border:medium double #000000; margin-left:10px; padding:5px; float:left;">
   CHS - Newsscript<br />
   <?=$lang['help']['version']?> 1.0<br />
   &copy; 2008 by Chrissyx<br />
   <a href="http://www.chrissyx.com/" target="_blank">http://www.chrissyx.com/</a>
  </div>
  <?php
  break;

# Administration: Startseite #
  default:
  include('language_homepage.php');
  echo('  <h4>' . $lang['homepage']['title'] . '</h4>
   ' . $lang['homepage']['intro'] . '<br /><br />
   <h5>' . $lang['homepage']['overview'] . '</h5>
   <ul style="list-style-type:square;">
    <li>' . $lang['homepage']['numofnews'] . ' ' . (count(file('../' . $settings[0]))-1) . '</li>
    <li>' . $lang['homepage']['numofcomments'] . ' ' . (count(glob('../' . $settings[3] . '*.dat'))) . '</li>
    <li>' . $lang['homepage']['numofuser'] . ' ' . (count(file('../' . $settings[2]))-1) . '</li>
    <li>' . $lang['homepage']['numofcats'] . ' ' . (count(file('../' . $settings[4]))-1) . '</li>' . (($settings[6]) ? '
    <li>' . $lang['homepage']['numofsmilies'] . ' ' . (count(file('../' . $settings[6]))-1) . '</li>' : '') . '
    <li>' . $lang['homepage']['numoflangs'] . ' ' . count(glob('*.ini')) . '</li>
   </ul>
');
  break;
 }
 echo("  </div>\n  ");
 newsTail();
 break;

# Installation #
 case 'install':
 include('language_install.php');
 newsHead('CHS - Newsscript: ' . $lang['install']['title'], 'Newsscript, CHS, ' . $lang['install']['title'] . ', Chrissyx', $lang['install']['title'] . ' des Newsscript von CHS', $lang['install']['charset'], $lang['install']['code']);
 echo('  ' . $lang['install']['startinstall'] . "<br />\n");
 if(($_POST['newsdat'] && $_POST['newsmax'] && $_POST['newspwsdat'] && $_POST['newscomments'] && $_POST['newscatsdat'] && $_POST['newscatpics'] && $_POST['name'] && preg_match('/[\.0-9a-z_-]+@[\.0-9a-z-]+\.[a-z]+/si', $_POST['email']) && $_POST['newspw']) && $_POST['tickermax'] && (substr($_POST['newspwsdat'], -4) == '.php') && ($_POST['newspw'] == $_POST['newspw2']))
 {
  $temp = fopen('settings.dat.php', 'w');
  fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . $_POST['newsdat'] . "\n" . $_POST['newsmax'] . "\n" . $_POST['newspwsdat'] . "\n" . $_POST['newscomments'] . "\n" . $_POST['newscatsdat'] . "\n" . $_POST['newscatpics'] . "\n" . $_POST['newssmilies'] . "\n" . $_POST['smiliepics'] . "\n" . $_POST['smiliesmax'] . "\n" . $_POST['smiliesmaxrow'] . "\n" . $_POST['tickermax'] . "\n" .  $_POST['redir']);
  fclose($temp);
  $temp = fopen('../' . $_POST['newsdat'], 'w');
  fwrite($temp, '1');
  fclose($temp);
  $temp = fopen('../' . $_POST['newspwsdat'], 'w');
  fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . $_POST['name'] . "\t" . md5($_POST['newspw']) . "\t1\t" . $_POST['email']);
  fclose($temp);
  mkdir('../' . $_POST['newscomments'], 0775);
  $temp = fopen('../' . $_POST['newscatsdat'], 'w');
  fwrite($temp, '1');
  fclose($temp);
  mkdir('../' . $_POST['newscatpics'], 0775);
  if($_POST['newssmilies'] && (substr($_POST['newssmilies'], -4) != '.var'))
  {
   $temp = fopen('../' . $_POST['newssmilies'], 'w');
   fwrite($temp, '1');
   fclose($temp);
   if($_POST['smiliepics'] != $_POST['newscatpics']) mkdir('../' . $_POST['smiliepics'], 0775);
  }
  unlink('language_install.php'); //Wird nicht mehr gebraucht
  echo('  ' . $lang['install']['endinstall'] . '<br /><br />
  ' . $lang['install']['note1'] . '<br /><br />
  <code>&lt;!-- CHS - Newsscript --&gt;&lt;?php include(\'news.php\'); ?&gt;&lt;!-- /CHS - Newsscript --&gt;</code><br /><br />
  ' . $lang['install']['note2'] . '<br /><br />
  <code>&lt;!-- CHS - Newsscript - Ticker --&gt;&lt;?php include(\'newsticker.php\'); ?&gt;&lt;!-- /CHS - Newsscript - Ticker --&gt;</code><br /><br />
  ' . sprintf($lang['install']['note3'], '<a href="http://www.chrissyx-forum.de.vu/" target="_blank">http://www.chrissyx-forum.de.vu/</a>') . '<br /><br />
  <a href="../news.php">' . $lang['install']['goto1'] . '</a> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '">' . $lang['install']['goto2'] . '</a> &ndash; <a href="' . (($_POST['redir']) ? $_POST['redir'] : 'http://' . $_SERVER['SERVER_NAME'] . '/') . '">' . $lang['install']['goto3'] . "</a>\n  ");
 }
 else echo('  <span class="b">ERROR:</span> ' . sprintf($lang['install']['error'], '<a href="' . $_SERVER['PHP_SELF'] . '">') . "</a>\n  ");
 newsTail();
 break;

 default:
 include('language_install.php');
 newsHead('CHS - Newsscript: ' . $lang['install']['title'], 'Newsscript, CHS, ' . $lang['install']['title'] . ', Chrissyx', $lang['install']['title'] . ' des Newsscript von CHS', $lang['install']['charset'], $lang['install']['code']);
 ?>

  <script type="text/javascript">
  function help(data)
  {

  /*******************************************************************\
  *Script written by Chrissyx                                         *
  *You may use and edit this script, if you don't remove this comment!*
  *http://www.chrissyx.de(.vu)/                                       *
  \*******************************************************************/

   document.getElementById('help').firstChild.nodeValue = data;
  };
  </script>

  <h3>CHS - Newsscript: <?=$lang['install']['title']?></h3>
  <?=$lang['install']['intro']?><br /><br />
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <table onmouseout="help('<?=$lang['install']['help']?>');">
   <tr><td colspan="2"></td><td rowspan="21" style="background-color:yellow; width:200px;"><div id="help"><?=$lang['install']['help']?></div></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help1']?>');"><td><?=$lang['install']['numofnews']?></td><td><input type="text" name="newsmax" value="20" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help2']?>');"><td><?=$lang['install']['locnews']?></td><td><input type="text" name="newsdat" value="newsscript/news.dat" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help3']?>');"><td><?=$lang['install']['locpws']?></td><td><input type="text" name="newspwsdat" value="newsscript/newspws.dat.php" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help17']?>');"><td><?=$lang['install']['foldcomments']?></td><td><input type="text" name="newscomments" value="newsscript/comments/" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help4']?>');"><td><?=$lang['install']['loccats']?></td><td><input type="text" name="newscatsdat" value="newsscript/newscats.dat" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help5']?>');"><td><?=$lang['install']['foldpics']?></td><td><input type="text" name="newscatpics" value="newsscript/catpics/" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help6']?>');"><td><?=$lang['install']['name']?></td><td><input type="text" name="name" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help7']?>');"><td><?=$lang['install']['email']?></td><td><input type="text" name="email" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help8']?>');"><td><?=$lang['install']['pass']?></td><td><input type="password" name="newspw" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help9']?>');"><td><?=$lang['install']['passrepeat']?></td><td><input type="password" name="newspw2" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help10']?>');"><td><?=$lang['install']['locsmilies']?></td><td><input type="text" name="newssmilies" size="25" onclick="this.value=(confirm('<?=$lang['install']['question']?>') ? 'forum/vars/smilies.var' : 'newsscript/newssmilies.dat');" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help11']?>');"><td><?=$lang['install']['foldsmilies']?></td><td><input type="text" name="smiliepics" id="smiliepics" onfocus="this.value='newsscript/smiliepics/';" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help12']?>');"><td><?=$lang['install']['numofsmilies']?></td><td><input type="text" name="smiliesmax" value="22" size="25" /></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help13']?>');"><td><?=$lang['install']['rowofsmilies']?></td><td><input type="text" name="smiliesmaxrow" value="11" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=$lang['install']['help18']?>');"><td><?=$lang['install']['numofticks']?></td><td><input type="text" name="tickermax" value="5" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=sprintf($lang['install']['help14'], $_SERVER['SERVER_NAME'])?>');"><td><?=$lang['install']['redir']?></td><td><input type="text" name="redir" size="25" onfocus="this.value='http://';" /></td></tr>
  </table>
  <input type="submit" value="<?=$lang['install']['install']?>" onmouseover="help('<?=$lang['install']['help15']?>');" /> <input type="reset" value="<?=$lang['install']['reset']?>" onmouseover="help('<?=$lang['install']['help16']?>');" />
  <input type="hidden" name="action" value="install" />
  </form>

  <?php
 newsTail();
 break;
}
?>