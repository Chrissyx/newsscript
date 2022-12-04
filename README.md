# Chrissyx Homepage Scripts - Newsscript

[![version](https://img.shields.io/badge/version-1.0.7.1-blue)](https://www.chrissyx.com/scripts.php#Newsscript)

## Introduction
File-based, fast, multilingual newsscript with BBCode, smilies, commenting, newsticker and dedicated admin panel incl. user, category, smiley administration and small statistic. Additional features are "Read on", list of references and newsticker supporting RSS feeds, internal listing and external embedding on other websites. Everything is configurable, starting with number of news per page, number of smilies (even for each row!) up to storage locations for the internal system files. Simple and easy installation with interactive help incl. translations for German and English. Continuous valid XHTML and due to caching really fast. Requires PHP 5.3 or higher and NO database.

## Requirements
* ![php](https://img.shields.io/badge/php-%3E%3D5.3-blue)
* ![webspace](https://img.shields.io/badge/webspace-chmod--able-lightgrey)

## Installation
The installation is simple as usual: Upload in that directory, in which your website is (and you're planning to use the Newsscript), the `news.php` file and the folder `newsscript` including its contents. Point your browser to the `index.php` in the `newsscript` folder and follow the instructions. If you would like to use the news ticker as well, upload the `newsticker.php` to the same location you've uploaded the `news.php` before.

## Update to new version
Upload, just like the installation, all files by replacing every file with its newer version. Point your browser to the `update.php` in the `newsscript` folder and follow the instructions.  
***IMPORTANT:*** Delete the `update.php` after updating the script!

## FAQ
- How to manage my news script?  
  Just point your browser to the `index.php` file in the `newsscript` folder, as you did during the installation and follow the instructions.

- I've forgot my password!  
  Go to the login form, you can request a new password there. The old one is still valid until you log in with the new password.

- How are these sources working?  
  Type in the link and add it. It's now saved and will disappear from the field. On the same way, you can remove the last added source with the link next to it. Click on "Preview" and then on the dropdown list with the arrow to check all available sources.

- Is it possible to translate the news script to another language?  
  Of course, copy an INI file and name it to the official language code corresponding to the desired language. E.g. `fr.ini` for French or `nl.ini` for Dutch. Start translating the strings between the quotation marks and check the hints at the beginning of the file. By having a complete translation, upload it to the `newsscript` folder and choose it from the language menu in the administration. Also please send it to me for providing it for other user! :slightly_smiling_face:

- What about the news ticker?  
  Just a quick overview for now: Use this code for internal listing of headlines on your homepage:  
  `<!-- CHS - Newsscript - Ticker --><?php include('newsticker.php'); ?><!-- /CHS - Newsscript - Ticker -->`  
  This example link will provide a RSS Newsfeed:  
  `https://www.mysite.tld/newsticker.php?type=rss`  
  This example code will provide the latest headlines for an external website:  
  `<script type="text/javascript" src="https://www.mysite.tld/newsticker.php?type=extern"></script>`  
  To change the number of displayed news apart from the setting you've entered during installation, add `&anz=X` to each link, e.g. 10 entries for the RSS Feed:  
  `https://www.mysite.tld/newsticker.php?type=rss&anz=10`  
  Or last 7 headlines for another website displaying your news:  
  `<script type="text/javascript" src="https://www.mysite.tld/newsticker.php?type=extern&anz=7"></script>`  
  You should provide your visitors these informations somewhere, thought.  
  Hope you got a clue about the features anyway. :wink:

- I'm getting a message like "ERROR: Datei/Ordner nicht gefunden!"?!?  
  Read again the install instructions carefully! Keep in mind, that folderpaths always have to end with `/`.

- I'm getting a message like "ERROR: Konnte keine Rechte setzen!"?!?  
  Set with your FTP program and chmod command the rights to `775` for the mentioned file/folder.

- I'm getting a "Warning: session_start(): Cannot send session cache limiter" warning?!?  
  Paste at the very beginning of your homepage (even before `<html>` or `<!DOCTYPE...`), on which you're running the news script, this code:
  `<?php session_start(); ?>`

- Is it possible to change the design of the news script?  
  The news script itself should already fit as far as possible to your own page design. If you would like to adjust more details of the news layout, you can use and fill out some predefined CSS classes, available since version 1.0.1:
  * newsscriptmain: For all news entries incl. single news viewing
  * newsscriptfooter: For the page navigation under the news listing
  * newsscriptcomments: For the whole comments area

  You can define those classes in the `<head>...</head>` section or include them in your own CSS file. For more information about CSS, see here: https://en.wikibooks.org/wiki/Cascading_Style_Sheets

- I'm getting a message "Fatal error: Call to undefined function imagecreatetruecolor()" while uploading a pic?!?  
  Automatic scaling of a category image needs the GD library loaded as PHP extension. If you don't have any access to the php.ini file to activate it, you have to adjust the image size manually before uploading.

- My question isn't answered here!  
  Sorry, no more FAQ entries for now. Please visit my board at https://www.chrissyx.com/forum/ for more help.  
  Or write me an email: chris@chrissyx.com

## BBCode reference
- [b]Bolded text[/b]
- [i]Italicized text[/i]
- [u]Underlined text[/u]
- [s]Strikethrough text[/s]
- [center]Centered paragraph[/center]
- [quote]Quotation[/quote]
- [url]Link[/url]
- [url=Link]Linked text[/url]
- [img]Image[/img]
- [img=Image]Description[/img]
- [email]Linked email address[/email]
- [email=Email address]Linked text[/email]
- [color=Color]Colored text[/color]
- [iframe]iFrame with 560x315 YouTube size[/iframe]
- [iframe=Width,Height]iFrame with given width and height[/iframe]
- [code]Source code[/code]
- [size=Size]With size scaled text[/size]
- [sup]Superscript text[/sup]
- [sub]Subscript text[/sub]
- [list][*]List entry[/list]

## Credits
© 2008-2022 by Chrissyx  
Powered by V4 Technology  
https://www.chrissyx.de/  
https://www.chrissyx.com/  
[![Twitter Follow](https://img.shields.io/twitter/follow/CXHomepage?style=social)](https://twitter.com/intent/follow?screen_name=CXHomepage)