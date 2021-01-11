<?php include('newsscript/core.php'); ?>
<html>
 <head>
  <title>DAU Webseite Test</title>
  <meta http-equiv="Content-Language" content="de">
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <meta http-equiv="Content-Script-Type" content="text/javascript">
 </head>
 <body>
  <table width="100%" border="1">
   <tr><th colspan="3">Header</th></tr>
   <tr><td colspan="3"><?php include('newsscript/newsticker.php'); ?></td></tr>
   <tr>
    <td width="10%">Menü 1</td>
    <td width="80%"><?php include('newsscript/news.php'); ?></td>
    <td width="10%">Menü 2</td>
   </tr>
   <tr><td colspan="3">Footer</td></tr>
  </table>
 </body>
</html>