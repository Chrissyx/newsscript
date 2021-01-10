<?php
 $_GET['anz'] = (!$_GET['anz']) ? 6 : $_GET['anz'];  //Standardanzahl der Newslinks hier ändern
 $suche = file("news.dat");
 $tag = date("d");
 $monat = date("m");
 $jahr = date("Y");
 $j = 0;

 switch ($_GET['type'])
 {
  case "rss":
  header("Content-type: application/rss+xml");
  echo("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>
<rss version=\"2.0\">
 <channel>
  <title>Chrissyx Homepage RSS Newsfeed</title>
  <link>http://" . $_SERVER['SERVER_NAME'] . "/</link>
  <description>Aktuellste News von Chrissyx Homepage</description>
  <pubDate>Tue, 24 May 2005 20:00:00 +0200</pubDate>
  <language>de-de</language>
  <copyright>Copyright 2005 by Chrissyx</copyright>
  <docs>http://www.w3.org/TR/REC-xml-names</docs>
  <image>
   <url>http://" . $_SERVER['SERVER_NAME'] . "/images/avatar.jpg</url>
   <title>Chrissyx Homepage</title>
   <link>http://" . $_SERVER['SERVER_NAME'] . "/</link>
   <width>64</width>
   <height>64</height>
  </image>\n");

  while ($j < $_GET['anz'])
  {
   if (in_array("\t<center><b>$tag.$monat.$jahr</b></center>\r\n", $suche) or in_array("\t<center><b>$tag.$monat.$jahr</b></center>\n", $suche) or in_array("\t<div class=\"center\"><span class=\"b\"><a name=\"$tag.$monat.$jahr\">$tag.$monat.$jahr</a></span></div>\n", $suche) or in_array("\t<div class=\"center\"><span class=\"b\"><a name=\"$tag.$monat.$jahr\">$tag.$monat.$jahr</a></span></div>\r\n", $suche))
   {
    echo ("  <item>\n   <title>$tag.$monat.$jahr</title>\n   <pubDate>" . strftime("%a, %d %b %Y %X", gmmktime(0, 0, 0, $monat, $tag, $jahr, -1)) . " +0200</pubDate>\n   <link>http://" . $_SERVER['SERVER_NAME'] . "/index.php#$tag.$monat.$jahr</link>\n   <description>News vom $tag.$monat.$jahr! Link anklicken, um sie zu lesen!</description>\n  </item>\n");
    $j++;
   }
   $tag--;
   if ($tag < 10) $tag = "0" . $tag;
   if ($tag <= 0)
   {
    $tag = 31;
    $monat--;
    if ($monat < 10) $monat = "0" . $monat;
    if ($monat <= 0)
    {
     $monat = 12;
     $jahr--;
     if ($jahr == 2001) break;
    }
   }
  }

  echo(" </channel>\n</rss>");
  break;

  case "extern":
  echo ("document.write('<!-- CHS Newsticker - Anfang -->');\n");
  while ($j < $_GET['anz'])
  {
   if (in_array("\t<center><b>$tag.$monat.$jahr</b></center>\r\n", $suche) or in_array("\t<center><b>$tag.$monat.$jahr</b></center>\n", $suche) or in_array("\t<div class=\"center\"><span class=\"b\"><a name=\"$tag.$monat.$jahr\">$tag.$monat.$jahr</a></span></div>\n", $suche) or in_array("\t<div class=\"center\"><span class=\"b\"><a name=\"$tag.$monat.$jahr\">$tag.$monat.$jahr</a></span></div>\r\n", $suche))
   {
    echo ("document.write('<a href=\"http://" . $_SERVER['SERVER_NAME'] . "/index.php#$tag.$monat.$jahr\" target=\"_blank\">$tag.$monat.$jahr</a>');\n");
    $j++;
   }
   $tag--;
   if ($tag < 10) $tag = "0" . $tag;
   if ($tag <= 0)
   {
    $tag = 31;
    $monat--;
    if ($monat < 10) $monat = "0" . $monat;
    if ($monat <= 0)
    {
     $monat = 12;
     $jahr--;
     if ($jahr == 2001) break;
    }
   }
  }
  echo ("document.write('<!-- /CHS Newsticker - Ende -->');\n");
  break;

  default:
  while ($j < $_GET['anz'])
  {
   if (in_array("\t<center><b>$tag.$monat.$jahr</b></center>\r\n", $suche) or in_array("\t<center><b>$tag.$monat.$jahr</b></center>\n", $suche) or in_array("\t<div class=\"center\"><span class=\"b\"><a name=\"$tag.$monat.$jahr\">$tag.$monat.$jahr</a></span></div>\n", $suche) or in_array("\t<div class=\"center\"><span class=\"b\"><a name=\"$tag.$monat.$jahr\">$tag.$monat.$jahr</a></span></div>\r\n", $suche))
   {
    echo ("     <a href=\"#$tag.$monat.$jahr\">$tag.$monat.$jahr</a><br />\n");
    $j++;
   }
   $tag--;
   if ($tag < 10) $tag = "0" . $tag;
   if ($tag <= 0)
   {
    $tag = 31;
    $monat--;
    if ($monat < 10) $monat = "0" . $monat;
    if ($monat <= 0)
    {
     $monat = 12;
     $jahr--;
     if ($jahr == 2001) break;
    }
   }
  }
  break;
 }
?>