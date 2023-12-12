<?php
/**
 * Adminmodul zum Installieren und Verwalten des Newsscripts.
 *
 * @author Chrissyx
 * @copyright (c) 2001-2022 by Chrissyx
 * @license https://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Newsscript
 * @version 1.0.7.1
 */
if(!is_dir('../newsscript/'))
    die('<b>ERROR:</b> Konnte Verzeichnis &quot;newsscript&quot; nicht finden!');
elseif(!file_exists('../news.php'))
    die('<b>ERROR:</b> Konnte &quot;news.php&quot; nicht finden!');
elseif(!file_exists('style.css'))
    die('<b>ERROR:</b> Konnte &quot;style.css&quot; nicht finden!');
else
    require('functions.php');

if(file_exists('settings.dat.php'))
{
    if(!isset($_SESSION['newspw'], $_SESSION['newsname'], $_SESSION['newsadmin']))
    {
        header('Location: ../news.php?action=admin');
        exit();
    }
    else
    {
        $settings = array_map('trim', array_slice(explode("\n", file_get_contents('settings.dat.php')), 1));
        $user = @array_map('trim', array_slice(file('../' . $settings[2]), 1)) or die('<b>ERROR:</b> Benutzer nicht gefunden!');
        $value = getUser($user, $_SESSION['newsname']) or die('<b>ERROR:</b> Admin nicht gefunden!');
        if($_SESSION['dispall'] || $value[2] != $_SESSION['newsadmin'] || $value[1] != $_SESSION['newspw'])
            die('<b>ERROR:</b> Keine Adminrechte!');
        $action = 'admin';
    }
}

if(!file_exists('language_index.php'))
{
    if(isset($_GET['inifile']))
        parseLanguage($_GET['inifile']);
    else
    {
        newsHead('CHS - Newsscript: Choose language', 'Newsscript, CHS, choose, language, Chrissyx', 'Choose the language for the Newsscript from CHS', 'UTF-8', 'en');
        echo('  <div class="center" style="width:99%; border:1px solid #000000; padding:5px; margin-bottom:1%;">' . "\n" . '   <h3>W&auml;hle eine Sprache / Choose a language:</h3>' . "\n");
        foreach(glob('*.ini') as $value)
            echo('   <a href="' . $_SERVER['PHP_SELF'] . '?inifile=' . $value . '">' . $value . '</a><br />' . "\n");
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
    <li><a href="' . ($settings[11] != '' ? $settings[11] : '../news.php') . '?action=newsout">' . $lang['index']['logout'] . ', ' . $_SESSION['newsname'] . '</a></li>
   </ul>
  </div>
  <div style="border:1px solid #000000; padding:5px; margin-left:1%; float:left;">
');
    //Wow, dieses krank-leetige Kontrukt wird benötigt, damit der Nutzer am Ende keine Entitäten in Textfeldern vorfindet, und trotzdem valide zu bleiben. Was proggt man nicht alles für maximalen Komfort und Idiotensicherheit...
    $htmlJSDecode = array_combine(array_keys($temp = array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES))+array('&#039;' => "'")), array_map(function($string)
    {
        return '\u00' . bin2hex($string);
    }, array_values($temp)));
    switch(isset($_GET['page']) ? $_GET['page'] : '')
    {

# Administration: Einstellungen #
        case 'settings':
        include('language_settings.php');
        if(isset($_POST['update']))
        {
            $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</p>\n";
            if(!$_POST['newsdat'])
                $settings[0] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['newsmax'])
                $settings[1] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['newspwsdat'] || (substr($_POST['newspwsdat'], -4) != '.php'))
                $settings[2] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['newscomments'])
                $settings[3] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['newscatsdat'])
                $settings[4] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['newscatpics'])
                $settings[5] .= '" style="border-color:#FF0000;';
            elseif($_POST['newssmilies'] && (substr($_POST['newssmilies'], -4) != '.var') && !$_POST['smiliepics'])
                $settings[7] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['tickermax'])
                $settings[10] .= '" style="border-color:#FF0000;';
            else
            {
                if($_POST['newsdat'] != $settings[0])
                    rename('../' . $settings[0], '../' . $_POST['newsdat']) or $_POST['newsdat'] = $settings[0];
                if($_POST['newspwsdat'] != $settings[2])
                    rename('../' . $settings[2], '../' . $_POST['newspwsdat']) or $_POST['newspwsdat'] = $settings[2];
                if($_POST['newscomments'] != $settings[3])
                    rename('../' . $settings[3], '../' . $_POST['newscomments']) or $_POST['newscomments'] = $settings[3]; #todo: verschieben in existierenden ordner?
                if($_POST['newscatsdat'] != $settings[4])
                    rename('../' . $settings[4], '../' . $_POST['newscatsdat']) or $_POST['newscatsdat'] = $settings[4];
                if($_POST['newscatpics'] != $settings[5])
                    rename('../' . $settings[5], '../' . $_POST['newscatpics']) or $_POST['newscatpics'] = $settings[5]; #todo: verschieben in existierenden ordner?
                //Drei Fälle: Keine smilies, smilies.var oder smilies.dat - Jeder Fall kann zu einen anderen werden.
                if($_POST['newssmilies'] != $settings[6])
                {
                    if($_POST['newssmilies'] && (substr($_POST['newssmilies'], -4) != '.var')) //Neu oder Update .dat
                    {
                        if(!$settings[6] || (substr($settings[6], -4) == '.var')) //Neu .dat
                        {
                            if(!file_exists('../' . dirname($_POST['newssmilies'])))
                                mkdir('../' . dirname($_POST['newssmilies']), 0755);
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
                        if(!@rmdir('../' . $settings[7]))
                        {
                            foreach(glob('../' . $settings[7] . '*.*') as $value)
                                unlink($value);
                            rmdir('../' . $settings[7]);
                        }
                        $_POST['smiliepics'] = ''; //Ordner muss auch weg
                        unlink('smilies.php'); //Gecachete Smilies auch
                    }
                }
                $temp = fopen('settings.dat.php', 'w');
                fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . $_POST['newsdat'] . "\n" . $_POST['newsmax'] . "\n" . $_POST['newspwsdat'] . "\n" . $_POST['newscomments'] . "\n" . $_POST['newscatsdat'] . "\n" . $_POST['newscatpics'] . "\n" . $_POST['newssmilies'] . "\n" . $_POST['smiliepics'] . "\n" . $_POST['smiliesmax']. "\n" . $_POST['smiliesmaxrow'] . "\n" . $_POST['tickermax'] . "\n" .  $_POST['redir'] . "\n" . (isset($_POST['captcha']) ? 'checked="checked" ' : ''));
                fclose($temp);
                $settings = array_map('trim', array_slice(explode("\n", file_get_contents('settings.dat.php')), 1));
                $temp = '   <p class="green">&raquo; ' . $lang['settings']['new'] . "</p>\n";
            }
        }
        else
            $temp = '';
?>
  <h4><?php echo($lang['settings']['title']); ?></h4>
   <p><?php echo($lang['settings']['intro']); ?></p>
<?php echo($temp); ?>   <form id="form" action="<?php echo($_SERVER['PHP_SELF']); ?>?page=settings" method="post">
   <table>
    <tr><td><?php echo($lang['settings']['numofnews']); ?></td><td><input type="text" name="newsmax" value="<?php echo($settings[1]); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['settings']['locnews']); ?></td><td><input type="text" name="newsdat" value="<?php echo($settings[0]); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['settings']['locpws']); ?></td><td><input type="text" name="newspwsdat" value="<?php echo($settings[2]); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['settings']['foldcomments']); ?></td><td><input type="text" name="newscomments" value="<?php echo($settings[3]); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['settings']['capcomments']); ?></td><td><input type="checkbox" name="captcha" <?php echo($settings[12]); ?>/></td></tr>
    <tr><td><?php echo($lang['settings']['loccats']); ?></td><td><input type="text" name="newscatsdat" value="<?php echo($settings[4]); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['settings']['foldpics']); ?></td><td><input type="text" name="newscatpics" value="<?php echo($settings[5]); ?>" size="25" /></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><?php echo($lang['settings']['locsmilies']); ?></td><td><input type="text" name="newssmilies" value="<?php echo($settings[6]); ?>" size="25" /></td></tr>
    <tr><td>(<?php echo($lang['settings']['foldsmilies']); ?></td><td><input type="text" name="smiliepics" value="<?php echo($settings[7]); ?>" size="25" />)</td></tr>
    <tr><td><?php echo($lang['settings']['numofsmilies']); ?></td><td><input type="text" name="smiliesmax" value="<?php echo($settings[8]); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['settings']['rowofsmilies']); ?></td><td><input type="text" name="smiliesmaxrow" value="<?php echo($settings[9]); ?>" size="25" /></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><?php echo($lang['settings']['numofticks']); ?></td><td><input type="text" name="tickermax" value="<?php echo($settings[10]); ?>" size="25" /></td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><?php echo($lang['settings']['redir']); ?></td><td><input type="text" name="redir" value="<?php echo($settings[11]); ?>" size="25" /></td></tr>
   </table>
   <br />
   <input type="submit" value="<?php echo($lang['index']['update']); ?>" /> <input type="reset" value="<?php echo($lang['index']['reset']); ?>" />
   <input type="hidden" name="update" value="true" />
   </form>
<?php
        break;

# Administration: Benutzerverwaltung #
        case 'user':
        include('language_user.php');
        if(isset($_POST['update']))
        {
            $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</p>\n";
            list($_POST['name'], $_POST['user']) = stripEscape($_POST['name'], $_POST['user']);
            if(!$_POST['name'])
                $_POST['name'] .= '" style="border-color:#FF0000;';
            elseif(!preg_match('/[\.0-9a-z_-]+@[\.0-9a-z-]+\.[a-z]+/si', $_POST['email']))
                $_POST['email'] .= '" style="border-color:#FF0000;';
            elseif($_POST['user'] && $_POST['name']) //Vorhandener User
            {
                $key = unifyUser($user, $_POST['user']);
                if(isset($_POST['delete']))
                    unset($user[$key]);
                else
                {
                    $value = explode("\t", $user[$key]);
                    $user[$key] = $_POST['name'] . "\t" . $value[1] . "\t" . (isset($_POST['isadmin']) && $_POST['isadmin'] == 'on' ? '1' :'0') . "\t" . $_POST['email'] . (isset($value[4]) ? "\t" . $value[4] : '');
                }
                saveUser('../' . $settings[2], $user);
                unset($_POST['name'], $_POST['email']);
                $temp = '   <p class="green">&raquo; ' . $lang['user']['edit'] . "</p>\n";
            }
            elseif($_POST['name'] && (unifyUser($user, $_POST['name']) === false)) //Neuer User
            {
                for($i=0,$newpw=''; $i<10; $i++)
                    $newpw .= chr(mt_rand(33, 126));
                $user[] = $_POST['name'] . "\t" . md5($newpw) . "\t" . (isset($_POST['isadmin']) && $_POST['isadmin'] == 'on' ? '1' :'0') . "\t" . $_POST['email'];
                $temp = fopen('../' . $settings[2], 'a');
                fwrite($temp, "\n" . end($user));
                fclose($temp);
                $temp = '   <p><span class="green">&raquo; ' . $lang['user']['new'] . ((mail($_POST['email'], $_SERVER['SERVER_NAME'] . ' Newsscript: ' . $lang['user']['subject'], sprintf($lang['user']['text'], $_POST['name'], $_SERVER['SERVER_NAME'], (isset($_POST['isadmin']) ? $lang['user']['admin'] : $lang['user']['poster']), $newpw), 'From: newsscript@' . $_SERVER['SERVER_NAME'] . "\n" . 'Reply-To: ' . $_POST['email'] . "\n" . 'X-Mailer: PHP/' . phpversion() . "\n" . 'Content-Type: text/plain; charset=' . $lang['index']['charset'])) ? ' ' . $lang['user']['send'] : '</span> <span style="color:#FF0000; font-weight:bold;">' . $lang['user']['nosend']) . "</span></p>\n"; #\r\n ???
                unset($newpw, $_POST['name'], $_POST['email']);
            }
            else
                $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . sprintf($lang['user']['exist'], $_POST['name']) . "</p>\n";
        }
        else
            $temp = '';
        echo("\n" . '   <script type="text/javascript">' . "\n");
        $temp2 = '   var user = new Array(';
        foreach($user as $key => $value)
        {
            $value = explode("\t", $value); //Entitäten zu Unicode-hex, siehe oben
            $temp2 .= 'new Array(\'' . strtr($value[0], $htmlJSDecode) . '\', ' . $value[2] . ', \'' . $value[3] . '\'), ';
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

   <h4><?php echo($lang['user']['title']); ?></h4>
   <p><?php echo($lang['user']['intro']); ?></p>
<?php echo($temp); ?>   <form action="<?php echo($_SERVER['PHP_SELF']); ?>?page=user" method="post">
   <table style="float:left;">
    <tr><td><?php echo($lang['user']['name']); ?></td><td><input type="text" name="name" id="name" value="<?php echo(isset($_POST['name']) ? $_POST['name'] : ''); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['user']['email']); ?></td><td><input type="text" name="email" id="email" value="<?php echo(isset($_POST['email']) ? $_POST['email'] : ''); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['user']['isadmin']); ?></td><td><input type="checkbox" name="isadmin" id="isadmin" /></td></tr>
    <tr><td><?php echo($lang['user']['delete']); ?></td><td><span style="background-color:#FF0000;"><input type="checkbox" name="delete" id="delete" disabled="disabled" /></span></td></tr>
   </table>
   <div style="border:1px solid #000000; margin-left:10px; padding:5px; float:left;">
    <?php echo($lang['user']['change']); ?><br />
<?php
        foreach($user as $key => $value)
        {
            $value = explode("\t", $value);
            echo('    <input type="radio" name="users" onclick="fillForm(' . $key . ');" />' . $value[0] . "<br />\n");
        }
?>   </div>
   <br style="clear:both;" /><br />
   <!-- delete warnung -->
   <input type="submit" value="<?php echo($lang['index']['update']); ?>" /> <input type="reset" value="<?php echo($lang['index']['reset']); ?>" onmouseup="document.getElementById('delete').disabled=true; document.getElementById('user').value='';" />
   <input type="hidden" name="update" value="true" />
   <input type="hidden" name="user" id="user" />
   </form>
<?php
        break;

# Administration: Kategorien #
        case 'cats':
        include('language_cats.php');
        $cats = array_map('trim', file('../' . $settings[4]));
        if(isset($_POST['update']))
        {
            $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</p>\n";
            list($_POST['catname'], $_POST['cat']) = stripEscape($_POST['catname'], $_POST['cat']);
            if(!$_POST['catname'])
                $_POST['catname'] .= '" style="border-color:#FF0000;';
            elseif($_FILES['uploadpic']['name'] && !preg_match("/(.*)\.(jpg|jpeg|gif|png|bmp)/i", $_FILES['uploadpic']['name']))
                $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
            elseif(isset($_POST['resize']) && !$_POST['width'])
                $_POST['width'] .= '" style="border-color:#FF0000;';
            elseif(isset($_POST['resize']) && !$_POST['height'])
                $_POST['height'] .= '" style="border-color:#FF0000;';
            else
            {
                switch($_FILES['uploadpic']['error'])
                {
                    case 0: //Mit Upload
                    if(move_uploaded_file($_FILES['uploadpic']['tmp_name'], $temp = '../' . $settings[5] . $_FILES['uploadpic']['name']))
                    {
                        chmod($temp, 0775);
                        if(isset($_POST['resize']))
                        {
                            if(!newsCreateThumbnail($temp, $temp, $_POST['width'], $_POST['height'], getimagesize($temp)))
                                $lang['cats']['resize'] = '<span style="color:#FFFF00; font-weight:bold;">' . $lang['cats']['warning'] . '</span></td><td style="color:#FFFF00; font-weight:bold;">' . $lang['cats']['scalefail'] . '</td></tr>
    <tr><td>' . $lang['cats']['resize'];
                            unset($_POST['resize'], $_POST['width'], $_POST['height']);
                        }
                        $_POST['catpic'] = $_FILES['uploadpic']['name'];
                    }
                    else
                    {
                        $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picprocess'] . "</p>\n";
                        break;
                    }

                    case 4: //Kein Upload
                    if($_POST['cat'] && $_POST['catname']) //Vorhandene Kategorie
                    {
                        $key = unifyCat($cats, $_POST['cat']);
                        $value = explode("\t", $cats[$key]);
                        if(isset($_POST['delete']))
                        {
                            if(isset($value[2]) && file_exists('../' . $settings[5] . $value[2]))
                                unlink('../' . $settings[5] . $value[2]);
                            unset($cats[$key]);
                        }
                        else
                        {
                            if(isset($value[2]) && ($_POST['catpic'] != $value[2]) && file_exists('../' . $settings[5] . $value[2]))
                                unlink('../' . $settings[5] . $value[2]);
                            $cats[$key] = $value[0] . "\t" . $_POST['catname'] . "\t" . $_POST['catpic'];
                        }
                        $temp = fopen('../' . $settings[4], 'w');
                        fwrite($temp, implode("\n", $cats));
                        fclose($temp);
                        unset($_POST['catname'], $_POST['catpic']);
                        $temp = '   <p class="green">&raquo; ' . $lang['cats']['edit'] . "</p>\n";
                    }
                    elseif($_POST['catname'] && !unifyCat($cats, $_POST['catname'])) //Neue Kategorie
                    {
                        $cats[] = $cats[0]++ . "\t" . $_POST['catname'] . "\t" . $_POST['catpic'];
                        $temp = fopen('../' . $settings[4], 'w');
                        fwrite($temp, implode("\n", $cats));
                        fclose($temp);
                        unset($_POST['catname'], $_POST['catpic']);
                        $temp = '   <p class="green">&raquo; ' . $lang['cats']['new'] . "</p>\n";
                    }
                    else
                        $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . sprintf($lang['cats']['exist'], $_POST['catname']) . "</p>\n";
                    break;

                    case 3:
                    $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picpartial'] . "</p>\n";
                    $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
                    break;

                    case 2:
                    case 1:
                    $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picbigsize'] . "</p>\n";
                    $_FILES['uploadpic']['name'].= '" style="border-color:#FF0000;';
                    break;

                    default:
                    $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picunknown'] . "</p>\n";
                    break;
                }
            }
        }
        else
            $temp = '';
        array_shift($cats); //Last CatID raus
        echo("\n" . '   <script type="text/javascript">' . "\n");
        $temp2 = '   var cats = new Array(';
        foreach($cats as $key => $value)
        {
            $value = explode("\t", $value); //Entitäten zu Unicode-hex, siehe oben
            $temp2 .= 'new Array(\'' . strtr($value[1], $htmlJSDecode) . '\', \'' . (isset($value[2]) ? $value[2] : '') . '\'), ';
        }
        echo($temp2 . "'Windows 98SE rulez');\n");
?>

   function fillForm(key)
   {
    document.getElementById('catname').value = cats[key][0];
    document.getElementById('catpic').value = cats[key][1];
    document.getElementById('pic').src = (cats[key][1] == '') ? 'frage.jpg' : ((cats[key][1].indexOf('/') == -1) ? '<?php echo('../' . $settings[5]); ?>' : ((cats[key][1].indexOf('//') == -1) ? '../' : '')) + cats[key][1]; //&amp;&amp; cats[key][1].substr(0, 3) == '../'
    document.getElementById('delete').disabled = false;
    document.getElementById('cat').value = cats[key][0];
   }
   </script>

   <h4><?php echo($lang['cats']['title']); ?></h4>
   <p><?php echo($lang['cats']['intro']); ?></p>
<?php echo($temp); ?>   <form action="<?php echo($_SERVER['PHP_SELF']); ?>?page=cats" method="post" enctype="multipart/form-data">
   <table style="float:left;">
    <tr><td><?php echo($lang['cats']['name']); ?></td><td><input type="text" name="catname" id="catname" value="<?php echo(isset($_POST['catname']) ? $_POST['catname'] : ''); ?>" size="45" /></td><td rowspan="5"><img src="frage.jpg" alt="CatPic" id="pic" /></td></tr>
    <tr><td><?php echo($lang['cats']['pic']); ?></td><td><input type="text" name="catpic" id="catpic" value="<?php echo(isset($_POST['catpic']) ? $_POST['catpic'] : ''); ?>" size="45" /></td></tr>
    <tr><td colspan="2"><?php echo($lang['cats']['hint1']); ?></td></tr>
    <tr><td><?php echo($lang['index']['upload']); ?></td><td><input type="file" name="uploadpic" value="<?php echo(isset($_FILES['uploadpic']['name']) ? $_FILES['uploadpic']['name'] : ''); ?>" size="25" onchange="document.getElementById('resize').disabled=this.value != '' ? false : true;" /></td></tr>
    <tr><td><?php echo($lang['cats']['resize']); ?></td><td><input type="checkbox" name="resize" id="resize"<?php echo(isset($_POST['resize']) ? ' checked="checked"' : ''); ?> disabled="disabled" /> <?php echo($lang['cats']['scaleto']); ?> <input type="text" name="width" id="width" size="2" value="<?php echo(isset($_POST['width']) ? $_POST['width'] : '64'); ?>" />x<input type="text" name="height" id="height" size="2" value="<?php echo(isset($_POST['height']) ? $_POST['height'] : '64'); ?>" /></td></tr>
    <tr><td><?php echo($lang['cats']['delete']); ?></td><td><span style="background-color:#FF0000;"><input type="checkbox" name="delete" id="delete" disabled="disabled" /></span></td></tr>
   </table>
   <div style="border:1px solid #000000; margin-left:10px; padding:5px; float:left;">
    <?php echo($lang['cats']['change']); ?><br />
<?php
        foreach($cats as $key => $value)
        {
            $value = explode("\t", $value);
            echo('    <input type="radio" name="cats" onclick="fillForm(' . $key . ');" />' . $value[1] . "<br />\n");
        }
?>   </div>
   <br style="clear:both;" />
   <?php echo(newsFont(2) . $lang['cats']['hint2']); ?></span><br /><br />
   <input type="submit" value="<?php echo($lang['index']['update']); ?>" /> <input type="reset" value="<?php echo($lang['index']['reset']); ?>" onmouseup="document.getElementById('delete').disabled=document.getElementById('resize').disabled=true; document.getElementById('pic').src='frage.jpg'; document.getElementById('cat').value='';" />
   <input type="hidden" name="update" value="true" />
   <input type="hidden" name="cat" id="cat" />
   </form>
<?php
        break;

# Administration: Smilies #
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
        else
            $smilies = array_map('trim', file('../' . $settings[6]));
        if(isset($_POST['update']))
        {
            $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['fillout'] . "</p>\n";
            list($_POST['synonym'], $_POST['smilie']) = stripEscape($_POST['synonym'], $_POST['smilie']);
            if(!$_POST['synonym'])
                $_POST['synonym'] .= '" style="border-color:#FF0000;';
            elseif(!$_POST['address'] && !$_FILES['uploadpic']['name'])
            {
                $_POST['address'] .= '" style="border-color:#FF0000;';
                $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
            }
            elseif($_FILES['uploadpic']['name'] && !preg_match("/(.*)\.(jpg|jpeg|gif|png|bmp)/i", $_FILES['uploadpic']['name']))
                $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
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
                        $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picprocess'] . "</p>\n";
                        break;
                    }

                    case 4: //Kein Upload
                    if($_POST['smilie'] && $_POST['synonym']) //Vorhandener Smilie
                    {
                        $key = unifySmilie($smilies, $_POST['smilie']);
                        $value = explode("\t", $smilies[$key]);
                        if(isset($_POST['delete']))
                        {
                            if(file_exists('../' . $settings[7] . $value[2])) unlink('../' . $settings[7] . $value[2]);
                            unset($smilies[$key]);
                        }
                        else
                        {
                            if(($_POST['address'] != $value[2]) && $value[2] && file_exists('../' . $settings[7] . $value[2]))
                                unlink('../' . $settings[7] . $value[2]);
                            $smilies[$key] = $value[0] . "\t" . $_POST['synonym'] . "\t" . $_POST['address'];
                        }
                        $temp = fopen('../' . $settings[6], 'w');
                        fwrite($temp, implode("\n", $smilies));
                        fclose($temp);
                        unset($_POST['synonym'], $_POST['address']);
                        $temp = '   <p class="green">&raquo; ' . $lang['smilies']['edit'] . "</p>\n";
                    }
                    elseif($_POST['synonym'] && !unifySmilie($smilies, $_POST['synonym'])) //Neuer Smilie
                    {
                        $smilies[] = $smilies[0]++ . "\t" . $_POST['synonym'] . "\t" . $_POST['address'];
                        $temp = fopen('../' . $settings[6], 'w');
                        fwrite($temp, implode("\n", $smilies));
                        fclose($temp);
                        unset($_POST['synonym'], $_POST['address']);
                        $temp = '   <p class="green">&raquo; ' . $lang['smilies']['new'] . "</p>\n";
                    }
                    else
                        $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . sprintf($lang['smilies']['exist'], $_POST['synonym']) . "</p>\n";
                    break;

                    case 3:
                    $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picpartial'] . "</p>\n";
                    $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
                    break;

                    case 2:
                    case 1:
                    $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picbigsize'] . "</p>\n";
                    $_FILES['uploadpic']['name'] .= '" style="border-color:#FF0000;';
                    break;

                    default:
                    $temp = '   <p style="color:#FF0000; font-weight:bold;">&raquo; ' . $lang['index']['picunknown'] . "</p>\n";
                    break;
                }
            }
        }
        else
            $temp = '';
        array_shift($smilies); //Last SmilieID raus
        echo("\n" . '   <script type="text/javascript">' . "\n");
        $temp2 = '   var smilies = new Array(';
        foreach($smilies as $key => $value)
        {
            $value = explode("\t", $value);
            $temp2 .= 'new Array(\'' . strtr($value[1], $htmlJSDecode) . '\', \'' . $value[2] . '\'), ';
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

   <h4><?php echo($lang['smilies']['title']); ?></h4>
   <p><?php echo($lang['smilies']['intro']); ?></p>
<?php echo($temp); ?>   <form action="<?php echo($_SERVER['PHP_SELF']); ?>?page=smilies" method="post" enctype="multipart/form-data">
   <table style="float:left;">
    <tr><td><?php echo($lang['smilies']['synoym']); ?></td><td><input type="text" name="synonym" id="synonym" value="<?php echo(isset($_POST['synonym']) ? $_POST['synonym'] : ''); ?>" size="45" /></td></tr>
    <tr><td><?php echo($lang['smilies']['adress']); ?></td><td><input type="text" name="address" id="address" value="<?php echo(isset($_POST['address']) ? $_POST['address'] : ''); ?>" size="45" /></td></tr>
    <tr><td colspan="2"><?php echo($lang['smilies']['hint1']); ?></td></tr>
    <tr><td><?php echo($lang['index']['upload']); ?></td><td><input type="file" name="uploadpic" value="<?php echo(isset($_FILES['uploadpic']['name']) ? $_FILES['uploadpic']['name'] : ''); ?>" size="25" /></td></tr>
    <tr><td><?php echo($lang['smilies']['delete']); ?></td><td><span style="background-color:#FF0000;"><input type="checkbox" name="delete" id="delete" disabled="disabled" /></span></td></tr>
   </table>
   <div style="border:1px solid #000000; margin-left:10px; padding:5px; float:left;">
    <?php echo($lang['smilies']['change']); ?><br />
<?php
        $i=0;
        foreach($smilies as $value)
        {
            $value = explode("\t", $value);
            echo('    <img src="' . ((strpos($value[2], '/') === false) ? '../' . $settings[7] : ((substr($value[2], 0, 3) == '../') ? '../' : '')) . $value[2] . '" alt="' . $value[1] . '" style="cursor:pointer;" onclick="fillForm(' . $i++ . ');" />');
            if(($i % $settings[9]) == 0)
                echo("<br />\n");
        }
?>   </div>
   <br style="clear:both;" />
   <?php echo(newsFont(2) . $lang['smilies']['hint2']); ?></span><br /><br />
   <input type="submit" value="<?php echo($lang['index']['update']); ?>" /> <input type="reset" value="<?php echo($lang['index']['reset']); ?>" onmouseup="document.getElementById('delete').disabled=true; document.getElementById('smilie').value='';" />
   <input type="hidden" name="update" value="true" />
   <input type="hidden" name="smilie" id="smilie" />
   </form>
<?php
        break;

# Administration: Sprache änden #
        case 'lang':
        include('language_lang.php');
        if(isset($_POST['inifile']))
        {
            parseLanguage($_POST['inifile']);
            include('language_lang.php');
            $temp = '   <p class="green">&raquo; ' . $lang['lang']['new'] . "</p>\n";
        }
        else
            $temp = '';
        echo('   <h4>' . $lang['lang']['title'] . '</h4>
' . $temp . '   <form action="' . $_SERVER['PHP_SELF'] . '?page=lang" method="post">
   ' . $lang['lang']['intro'] . ' <select name="inifile">
');
        foreach(glob('*.ini') as $value)
            echo('    <option>' . $value . "</option>\n");
        echo('   </select><br /><br />
   <input type="submit" value="' . $lang['index']['update'] . '" />
   </form>
');
        break;

# Administration: Hilfe & Infos #
        case 'help':
        include('language_help.php');
        ini_set('default_socket_timeout', 3); //Timeout für Updatecheck #stream_context_create() ab PHP5
?>
  <h4><?php echo($lang['help']['title']); ?></h4>
  <div style="padding-right:5px; float:left;">
   <p><?php echo($lang['help']['check'] . ' ' . (@file_get_contents('https://www.chrissyx.com/update.php?nsversion=' . getNewsVersion()) == 'true' ? $lang['help']['newer'] : $lang['help']['latest'])); ?></p>
   <p><?php echo($lang['help']['hint1']); ?><br />
      <a href="https://www.chrissyx.com/scripts.php" target="_blank">https://www.chrissyx.com/scripts.php</a></p>
   <p><?php echo($lang['help']['hint2']); ?><br />
      <a href="https://www.chrissyx.com/forum/" target="_blank">https://www.chrissyx.com/forum/</a></p>
   <p><a href="https://validator.w3.org/check?uri=referer" target="_blank"><img src="https://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" style="vertical-align:middle;" /></a> &ndash; <a href="https://jigsaw.w3.org/css-validator/check/referer" target="_blank"><img src="https://jigsaw.w3.org/css-validator/images/vcss" alt="CSS ist valide!" style="vertical-align:middle;" /></a><?php echo((file_exists('../newsticker.php') ? ' &ndash; <a href="https://feedvalidator.org/check.cgi?url=http://' . $_SERVER['SERVER_NAME'] . substr(dirname($_SERVER['PHP_SELF']), 0, strrpos(dirname($_SERVER['PHP_SELF']), '/')) . '/newsticker.php?type=rss" target="_blank"><img src="valid-rss.png" alt="[Valid RSS]" title="Validate my RSS feed" style="vertical-align:middle;" /></a>' : '')); ?></p>
  </div>
  <div style="border:medium double #000000; margin-left:10px; padding:5px; float:left;">
   <p>CHS - Newsscript<br />
      <?php echo($lang['help']['version'] . ' ' . getNewsVersion() . newsFont(1)); ?> / PHP: <?php echo(phpversion()); ?></span><br />
      &copy; 2008&ndash;2023 by Chrissyx<br />
      <a href="https://www.chrissyx.com/" target="_blank">https://www.chrissyx.com/</a></p>
  </div>
<?php
        break;

# Administration: Startseite #
        default:
        include('language_homepage.php');
        echo('  <h4>' . $lang['homepage']['title'] . '</h4>
   <p>' . $lang['homepage']['intro'] . '</p>
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
        fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . $_POST['newsdat'] . "\n" . $_POST['newsmax'] . "\n" . $_POST['newspwsdat'] . "\n" . $_POST['newscomments'] . "\n" . $_POST['newscatsdat'] . "\n" . $_POST['newscatpics'] . "\n" . $_POST['newssmilies'] . "\n" . $_POST['smiliepics'] . "\n" . $_POST['smiliesmax'] . "\n" . $_POST['smiliesmaxrow'] . "\n" . $_POST['tickermax'] . "\n" .  $_POST['redir'] . "\n" . (isset($_POST['captcha']) ? 'checked="checked" ' : ''));
        fclose($temp);
        if(!file_exists('../' . dirname($_POST['newsdat'])))
            mkdir('../' . dirname($_POST['newsdat']), 0775);
        $temp = fopen('../' . $_POST['newsdat'], 'w');
        fwrite($temp, '1');
        fclose($temp);
        if(!file_exists('../' . dirname($_POST['newspwsdat'])))
            mkdir('../' . dirname($_POST['newspwsdat']), 0775);
        $temp = fopen('../' . $_POST['newspwsdat'], 'w');
        fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . stripEscape($_POST['name']) . "\t" . md5($_POST['newspw']) . "\t1\t" . $_POST['email']);
        fclose($temp);
        if(!file_exists('../' . $_POST['newscomments']))
            mkdir('../' . $_POST['newscomments'], 0775);
        if(!file_exists('../' . dirname($_POST['newscatsdat'])))
            mkdir('../' . dirname($_POST['newscatsdat']), 0775);
        $temp = fopen('../' . $_POST['newscatsdat'], 'w');
        fwrite($temp, '1');
        fclose($temp);
        if(!file_exists('../' . $_POST['newscatpics']))
            mkdir('../' . $_POST['newscatpics'], 0775);
        if($_POST['newssmilies'] != '' && substr($_POST['newssmilies'], -4) != '.var')
        {
            $temp = fopen('../' . $_POST['newssmilies'], 'w');
            fwrite($temp, '1');
            fclose($temp);
            if($_POST['smiliepics'] != $_POST['newscatpics'])
                mkdir('../' . $_POST['smiliepics'], 0775);
        }
        $temp = fopen('version.dat.php', 'w');
        fwrite($temp, "<?php die('<b>ERROR:</b> Keine Rechte!'); ?>\n" . getNewsVersion());
        fclose($temp);
        unlink('language_install.php'); //Wird nicht mehr gebraucht
        unlink('update.php'); //Update wird auch nicht gebraucht
        echo('  ' . $lang['install']['endinstall'] . '<br /><br />
  <p>' . $lang['install']['note1'] . '</p>
  <p><code>&lt;!-- CHS - Newsscript --&gt;&lt;?php include(\'news.php\'); ?&gt;&lt;!-- /CHS - Newsscript --&gt;</code></p>
  <p>' . $lang['install']['note2'] . '</p>
  <p><code>&lt;!-- CHS - Newsscript - Ticker --&gt;&lt;?php include(\'newsticker.php\'); ?&gt;&lt;!-- /CHS - Newsscript - Ticker --&gt;</code></p>
  <p>' . sprintf($lang['install']['note3'], '<a href="https://www.chrissyx.com/forum/" target="_blank">https://www.chrissyx.com/forum/</a>') . '</p>
  <p><a href="../news.php">' . $lang['install']['goto1'] . '</a> &ndash; <a href="' . $_SERVER['PHP_SELF'] . '"><span class="b">' . $lang['install']['goto2'] . '</span></a> &ndash; <a href="' . ($_POST['redir'] ? $_POST['redir'] : 'http://' . $_SERVER['SERVER_NAME'] . '/') . '">' . $lang['install']['goto3'] . "</a></p>\n  ");
    }
    else
        echo('  <p><span class="b">ERROR:</span> ' . sprintf($lang['install']['error'], '<a href="' . $_SERVER['PHP_SELF'] . '">') . "</a></p>\n  ");
    newsTail();
    break;

    default:
    include('language_install.php');
    newsHead('CHS - Newsscript: ' . $lang['install']['title'], 'Newsscript, CHS, ' . $lang['install']['title'] . ', Chrissyx', $lang['install']['title'] . ' des Newsscript von CHS', $lang['install']['charset'], $lang['install']['code']);
?>

  <script type="text/javascript">
  function help(data)
  {
   document.getElementById('help').firstChild.nodeValue = data;
  }
  </script>

  <h3>CHS - Newsscript: <?php echo($lang['install']['title']); ?></h3>
  <p><?php echo($lang['install']['intro']); ?></p>
  <form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post">
  <table onmouseout="help('<?php echo($lang['install']['help']); ?>');">
   <tr><td colspan="2"></td><td rowspan="23" style="background-color:#FFFF00; width:200px;"><div class="center" id="help"><?php echo($lang['install']['help']); ?></div></td></tr>
   <tr><th colspan="2"><?php echo($lang['install']['general']); ?></th></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help1']); ?>');"><td><?php echo($lang['install']['numofnews']); ?></td><td><input type="text" name="newsmax" value="20" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help2']); ?>');"><td><?php echo($lang['install']['locnews']); ?></td><td><input type="text" name="newsdat" value="newsscript/news.dat" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help3']); ?>');"><td><?php echo($lang['install']['locpws']); ?></td><td><input type="text" name="newspwsdat" value="newsscript/newspws.dat.php" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help17']); ?>');"><td><?php echo($lang['install']['foldcomments']); ?></td><td><input type="text" name="newscomments" value="newsscript/comments/" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help19']); ?>');"><td><?php echo($lang['install']['capcomments']); ?></td><td><input type="checkbox" name="captcha" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help4']); ?>');"><td><?php echo($lang['install']['loccats']); ?></td><td><input type="text" name="newscatsdat" value="newsscript/newscats.dat" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help5']); ?>');"><td><?php echo($lang['install']['foldpics']); ?></td><td><input type="text" name="newscatpics" value="newsscript/catpics/" size="25" /></td></tr>
   <tr><th colspan="2"><?php echo($lang['install']['youracc']); ?></th></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help6']); ?>');"><td><?php echo($lang['install']['name']); ?></td><td><input type="text" name="name" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help7']); ?>');"><td><?php echo($lang['install']['email']); ?></td><td><input type="text" name="email" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help8']); ?>');"><td><?php echo($lang['install']['pass']); ?></td><td><input type="password" name="newspw" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help9']); ?>');"><td><?php echo($lang['install']['passrepeat']); ?></td><td><input type="password" name="newspw2" size="25" /></td></tr>
   <tr><th colspan="2"><?php echo($lang['install']['smilies']); ?></th></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help10']); ?>');"><td><?php echo($lang['install']['locsmilies']); ?></td><td><input type="text" name="newssmilies" size="25" onclick="this.value=(confirm('<?php echo($lang['install']['question']); ?>') ? 'forum/vars/smilies.var' : 'newsscript/newssmilies.dat');" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help11']); ?>');"><td><?php echo($lang['install']['foldsmilies']); ?></td><td><input type="text" name="smiliepics" id="smiliepics" onfocus="this.value='newsscript/smiliepics/';" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help12']); ?>');"><td><?php echo($lang['install']['numofsmilies']); ?></td><td><input type="text" name="smiliesmax" value="22" size="25" /></td></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help13']); ?>');"><td><?php echo($lang['install']['rowofsmilies']); ?></td><td><input type="text" name="smiliesmaxrow" value="11" size="25" /></td></tr>
   <tr><th colspan="2"><?php echo($lang['install']['newsticker']); ?></th></tr>
   <tr onmouseover="help('<?php echo($lang['install']['help18']); ?>');"><td><?php echo($lang['install']['numofticks']); ?></td><td><input type="text" name="tickermax" value="5" size="25" /></td></tr>
   <tr><th colspan="2"><?php echo($lang['install']['misc']); ?></th></tr>
   <tr onmouseover="help('<?php echo(sprintf($lang['install']['help14'], $_SERVER['SERVER_NAME'])); ?>');"><td><?php echo($lang['install']['redir']); ?></td><td><input type="text" name="redir" size="25" onfocus="this.value='http://';" /></td></tr>
  </table>
  <input type="submit" value="<?php echo($lang['install']['install']); ?>" onmouseover="help('<?php echo($lang['install']['help15']); ?>');" /> <input type="reset" value="<?php echo($lang['install']['reset']); ?>" onmouseover="help('<?php echo($lang['install']['help16']); ?>');" />
  <input type="hidden" name="action" value="install" />
  </form>

<?php
    newsTail();
    break;
}
?>