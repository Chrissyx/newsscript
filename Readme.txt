########################################
#Chrissyx Homepage Scripts - Newsscript#
########################################


Version: 1.0.3.5


ENGLISH - SCROLL DOWN!

Vorwort
Datei-basiertes, schnelles, mehrsprachiges Newsscript mit BBCode, Smilies, Kommentarfunktion, Newsticker und
eigener Adminoberfläche inkl. Nutzer-, Kategorien-, Smilieverwaltung und kleiner Statistik. Weitere Features
für "Weiterlesen", Quellenangaben und ein Newsticker für RSS Newsfeeds, interne Anzeige und externe Einbindung
auf anderen Webseiten. Alles stark einstellbar, von der Anzahl der gezeigten News pro Seite, über Anzahl der
Smilies (auch pro Reihe) bis hin zu den Speicherorten der internen Systemdateien. Einfache Installation mit
interaktiver Hilfe, inkl. Übersetzungen für Deutsch und Englisch. Durchgehend valides XHTML und dank
Cache-Funktionen sehr schnell. Benötigt PHP ab V4.3 und KEINE Datenbank.


Vorraussetzungen
-PHP ab 4.3
-chmod fähiger Webspace


Installation
Die Installation ist gewohnt einfach: Lade in dem Ordner, wo deine Webseite ist (auf welcher das Newsscript zum
Einsatz kommen soll), die "news.php" und den Ordner "newsscript" samt Inhalt hoch. Rufe danach die "index.php"
aus dem Ordner "newsscript" auf und folge dann den Anweisungen. Wenn Du auch den Newsticker nutzen möchtest,
lade die "newsticker.php" dahin hoch, wo die "news.php" schon ist.


Update auf neue Version
Lade, wie schon zur Installation auch, alle Dateien hoch und ersetze so jede Datei durch ihre neue Version.
Rufe danach die "update.php" aus dem Ordner "newsscript" auf und folge den Anweisungen.
WICHTIG: Nach dem Update die "update.php" wieder löschen!


FAQ
-Wie kann ich mein Newsscript verwalten?
Rufe, wie schon bei der Installation auch, die "index.php" im "newsscript"-Ordner auf und folge den Anweisungen.

-Ich habe mein Passwort vergessen!
Begib dich ganz normal zum Login, dort kannst Du dir auch ein neues Passwort zu schicken lassen. Das alte bleibt
weiterhin gültig, bis Du dich mit dem neuen eingeloggt hast.

-Wie funktioniert das mit den Quellen?
Gib den Link zur Seite ein und füge ihn hinzu, danach ist er gespeichert und wird nicht mehr im Feld angezeigt.
Genau so kannst Du die jeweils letzte Quelle wieder löschen mit dem Link daneben. Klicke auf "Vorschau", um
alle vorhandenen Quellen einzusehen, und dann auf das Dropdown-Menü mit dem Pfeil.

-Kann ich das Newsscript auch in andere Sprachen übersetzen?
Aber sicher: Kopiere dir eine INI Datei und benenne sie in das offizielle Sprachkürzel der jeweiligen Sprache um.
Z.B. "fr.ini" für Französisch oder "nl.ini" für Niederländisch. Übersetzte dann die Texte in den Anführungszeichen
und achte dabei auf die Hinweise am Anfang der Datei. Wenn Du eine vollständige Übersetzung hast, lade sie in den
"newsscript"-Ordner und wähle sie im Sprachmenü der Administration aus. Bitte schick sie mir auch, so dass ich
anderen diese ebenfalls zur Verfügung stellen kann! :)

-Was kann alles der Newsticker?
Er bietet eine Auflistung der letzten X News für deine Seite selbst, eine RSS Newsfeed Anbindung und sogar die
Möglichkeit, deine Newstitel auf anderen Webseiten einzubinden. Für die interne Einbindung, füge diesen Code
an der gewünschten Stelle in den Quelltext deiner Seite ein:
<!-- CHS - Newsscript - Ticker --><?php include('newsticker.php'); ?><!-- /CHS - Newsscript - Ticker -->
Für die RSS Anbindung solltest Du deinen Besuchern einen Link mit einer passenden Grafik zur Verfügung stellen.
Der Link ergibt sich natürlich aus dem Ort, wo Du den Ticker bzw. das Script betreibst. Auf jeden Fall endet
er mit "/newsticker.php?type=rss", z.B. "http://www.meineseite.tld/newsticker.php?type=rss". Es bietet sich an,
den Link unmittelbar neben der internen Ausgabe des Newstickers zu platzieren.
Die Einbindung der letzten News auf anderen Webseiten erfolgt ähnlich, d.h. anstatt "type=rss" einfach
"type=extern" anhängen. Lautet der Link also z.B. "http://www.meineseite.tld/newsticker.php?type=extern", so
muss man
<script type="text/javascript" src="http://www.meineseite.tld/newsticker.php?type=extern"></script>
in den Quellcode seiner Seite einbinden für eine Anzeige deiner letzten News. Willst Du diesen Service anbieten,
so musst Du den Code auf deiner Seite präsentieren und natürlich den Link wie oben beschrieben vorher anpassen.
Die Anzahl der gezeigten News hängt im Wesentlichen von der Einstellung ab, die Du während der Installation triffst.
Allerdings kann man im externen Modus und beim RSS Feed diese nochmals individualisieren, d.h. wenn Du z.B. die
letzten 5 News bei deinen internen Ticker ausgeben lässt, so werden es auch 5 beim RSS Newsfeed und der externen
Ausgabe sein. Um nun eine höhere Anzahl zu ermöglichen, kann man einfach die Anzahl per "&anz=X" angeben, in dem
man es an den vorhandenen Link hängt. Z.B. für 10 News im RSS Feed:
http://www.meineseite.tld/newsticker.php?type=rss&anz=10
Oder 7 News bei der externen Einbindung:
<script type="text/javascript" src="http://www.meineseite.tld/newsticker.php?type=extern&anz=7"></script>
Auch das sollte man an geeigneter Stelle den Interessierten mitteilen.

-Ich erhalte beim Aufruf die Meldung "ERROR: Datei/Ordner nicht gefunden!"?!?
Lies dir die Installationsanleitung hier genaustens durch! Achte darauf, dass Ordnerpfade immer mit "/" enden.

-Ich erhalte beim Aufruf die Meldung "ERROR: Konnte keine Rechte setzen!"?!?
Setze mit deinem FTP Programm per chmod Befehl die Rechte auf "775" für die/den angegebene/n Datei/Ordner.

-Es kommt beim Aufruf eine "Warning: session_start(): Cannot send session cache limiter" Warnung?!?
Füge ganz am Anfang deiner Seite (also noch vor "<html>" bzw. "<!DOCTYPE..."), auf welcher Du das Newsscript
eingesetzt hast, das ein:
<?php session_start(); ?>

-Kann ich das Design vom Newsscript anpassen?
Das Newsscript selber sollte sich schon weitestgehend deinem Seitendesign anpassen. Wenn Du aber weitere Feinheiten
an der Darstellung der News vornehmen möchtest, stehen dir seit Version 1.0.1 vordefinierte CSS Klassen zur
Verfügung, deren Inhalt Du selber bestimmen kannst:
newsscriptmain: Für alle Newseinträge inkl. Einzelansicht
newsscriptfooter: Für die Seitennavigation unter den News
newsscriptcomments: Für den kompletten Kommentarbereich
Diese Klassen kannst Du dann bei Bedarf im <head>...</head> Bereich oder in deiner eigenen CSS Datei einbinden.
Mehr zum Thema CSS findest Du hier: http://de.selfhtml.org/css/formate/einbinden.htm

-Meine Frage wurde nicht beantwortet!
Dann besuch mein Forum unter http://www.chrissyx-forum.de.vu/ oder schreib mir eine E-Mail: chris@chrissyx.com


BBCode Referenz
-[b]Fetter Text[/b]
-[i]Kursiver Text[/i]
-[u]Unterstrichender Text[/u]
-[s]Durchgestrichender Text[/s]
-[center]Zentrierter Absatz[/center]
-[quote]Zitat[/quote]
-[url]Link[/url]
-[url=Link]Verlinkter Text[/url]
-[img]Bild[/img]
-[img=Bild]Beschreibung[/img]
-[email]Verlinkte E-Mail Adresse[/email]
-[email=E-Mail Adresse]Verlinkter Text[/email]
-[color=Farbe]Farbiger Text[/color]
-[flash]Flash mit 425x355 YouTube Größe[/flash]
-[flash=Breite,Höhe]Flash mit vorgegebener Breite und Höhe[/flash]
-[code]Quellcode[/code]
-[size=Größe]Um Größe skalierter Text[/size]
-[sup]Hochgestellter Text[/sup]
-[sub]Tiefgestellter Text[/sub]


Credits
© 2008, 2009 by Chrissyx
Powered by V4 Technology
http://www.chrissyx.de(.vu)/
http://www.chrissyx.com/

-----------------------------------------------------------------------------------------------------------------------

Requirements
-PHP 4.3 or higher
-chmod-able webspace


Installation
The installation is simple as usual: Upload in that directory, in which your website is (and you're planning to use
the Newsscript), the "news.php" file and the folder "newsscript" including its contents. Point your browser to the
"index.php" in the "newsscript" folder and follow the instructions. If you would like to use the news ticker as well,
upload the "newsticker.php" to the same location you've uploaded the "news.php" before.


Update to new version
Upload, just like the installation, all files by replacing every file with its newer version. Point your browser to
the "update.php" in the "newsscript" folder and follow the instructions. IMPORTANT: Delete the "update.php" after
updating the script!


FAQ
-How to manage my news script?
Just point your browser to the "index.php" file in the "newsscript" folder, as you did during the installation and
follow the instructions.

-I've forgot my password!
Go to the login form, you can request a new password there. The old one is still valid until you log in with the new
password.

-How are these sources working?
Type in the link and add it. It's now saved and will disappear from the field. On the same way, you can remove the
last added source with the link next to it. Click on "Preview" and then on the dropdown list with the arrow to check
all available sources.

-Is it possible to translate the news script to another language?
Of course, copy an INI file and name it to the official language code corresponding to the desired language.
E.g. "fr.ini" for french or "nl.ini" for dutch. Start translating the strings between the quotation marks and check
the hints at the beginning of the file. By having a complete translation, upload it to the "newsscript" folder and
choose it from the language menu in the administration. Also please send it to me for providing it for other user! :)

-What about the news ticker?
Just a quick overview for now: Use this code for internal listing of headlines on your homepage:
<!-- CHS - Newsscript - Ticker --><?php include('newsticker.php'); ?><!-- /CHS - Newsscript - Ticker -->
This example link will provide a RSS Newsfeed:
http://www.mysite.tld/newsticker.php?type=rss
This example code will provide the latest headlines for an external website:
<script type="text/javascript" src="http://www.mysite.tld/newsticker.php?type=extern"></script>
To change the number of displayed news apart from the setting you've entered during installation, add "&anz=X" to
each link, e.g. 10 entries for the RSS Feed:
http://www.mysite.tld/newsticker.php?type=rss&anz=10
Or last 7 headlines for another website displaying your news:
<script type="text/javascript" src="http://www.mysite.tld/newsticker.php?type=extern&anz=7"></script>
You should provide your visitors these informations somewhere, thought.
Hope you got a clue about the features anyway. ;)

-I'm getting a message like "ERROR: Datei/Ordner nicht gefunden!"?!?
Read again the install instructions carefully! Keep in mind, that folderpaths always have to end with "/".

-I'm getting a message like "ERROR: Konnte keine Rechte setzen!"?!?
Set with your FTP program and chmod command the rights to "775" for the mentioned file/folder.

-I'm getting a "Warning: session_start(): Cannot send session cache limiter" warning?!?
Paste at the very beginning of your homepage (even before "<html>" or "<!DOCTYPE..."), on which you're running the
news script, this code:
<?php session_start(); ?>

-Is it possible to change the design of the news script?
The news script itself should already fit as far as possible to your own page design. If you would like to adjust
more details of the news layout, you can use and fill out some predefined CSS classes, available since version 1.0.1:
newsscriptmain: For all news entries incl. single news viewing
newsscriptfooter: For the page navigation under the news listing
newsscriptcomments: For the whole comments area
You can define those classes in the <head>...</head> section or include them in your own CSS file. For more
information about CSS, see here: http://en.wikibooks.org/wiki/CSS_Programming

-My question isn't answered here!
Sorry, no more FAQ entries for now. Please visit my board at http://www.chrissyx.com/forum/ for more help.
Or write me an e-mail: chris@chrissyx.com


BBCode reference
-[b]Bolded text[/b]
-[i]Italicized text[/i]
-[u]Underlined text[/u]
-[s]Strikethrough text[/s]
-[center]Centered paragraph[/center]
-[quote]Quotation[/quote]
-[url]Link[/url]
-[url=Link]Linked text[/url]
-[img]Image[/img]
-[img=Image]Description[/img]
-[email]Linked e-mail address[/email]
-[email=E-mail address]Linked text[/email]
-[color=Color]Colored text[/color]
-[flash]Flash with 425x355 YouTube size[/flash]
-[flash=Width,Height]Flash with given width and height[/flash]
-[code]Source code[/code]
-[size=Size]With size scaled text[/size]
-[sup]Superscript text[/sup]
-[sub]Subscript text[/sub]


Credits
© 2008, 2009 by Chrissyx
Powered by V4 Technology
http://www.chrissyx.de(.vu)/
http://www.chrissyx.com/