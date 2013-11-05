Latte - Min version av ett PHP-baserat och MVC-inspirerat ramverk
=================================================================

Gjort av Andreas Carlsson, andreasc89@gmail.com som en del i kursen "Databasdrivna webbapplikationer med PHP och MVC" på Blekinge Tekniska Högskola.
Baserat på ramverket Lydia av Mikael Roos, lärare på BTH.


Specifikationer
---------------

* PHP, minst version 5.0
* MySQL-databas
* Katalogen site/src ska ha rättigheter 777


Installation
------------

För att installera Latte laddar du ner hela ramverket från GitHub och ladda upp på din server. Öppna upp en webbläsare och navigera till katalogen där du installerade. Du får nu upp en välkomstsida med en länk till installations-kontrollern, du kan också navigera till `/setup` för att komma dit. Här görs en kontroll som säkerställer att du har rätt version av PHP installerad och att katalogen site/data är skrivbar. Om allt är okej får du upp ett formulär där du fyller i dina uppgifter till MySQL.

Det går också att göra en manuell installation genom att skriva in följande information i en php-fil och lägga den i mappen site/data med namnet __dbconfig.php__

    <?php
    $host = 'databas-server';
    $dbname = 'namn på databasen';
    $user = 'användarnamn';
    $pass = 'lösenord';
    ?>
    
När databasen är inlagd och uppkopplingen fungerar kan du köra initieringsmetoderna för att skapa tabeller och lägga till exempel-innehåll i dessa. Går du genom installationsförfarandet kommer du automatiskt hit när installationen av databasen är slutförd. Du kan också navigera till `/setup/install`.

Användning
----------

När ramverket är installerat och klart kan du klicka på Logga in längst upp till höger. Användarnamnet för administratörskontot är __root__ och lösenordet __root__. Det finns också en användare som utan administratörsrättigheter som bara tillhör gruppen user. Den här kan du logga in som med användarnamn __user__ och lösenord __user__.

I index-kontrollern listas alla kontrollers och deras metoder i vänsterspalten. Det finns också en inbyggd dokumentation i ramverket som ligger i kontroller ´module´. Här listas alla moduler i vänsterspalten, klicka på respektive modul för att läsa mer om den.

Här följer lite exempel på vad du kan göra:

### Ändra logo, webbplatsens titel, footer och navigeringsmeny ###

Latte är enkelt att anpassa precis som man vill ha det. Vi utgår 


### Skapa en blogg ###

asdf


### Skapa en sida ###

En sida skapar du enkelt genom att gå till kontrollern ´content/create´. Det finns även en länk högst upp på sidan som går direkt till ´content´-kontrollern. Här fyller du i titel, länktext, innehåll, typ och filter. En kort förklaring till vad dessa innebär:

* __Title:__ Titel och rubrik på sidan
* __Linktext:__ En enkel länktext till sidan. Får innehålla gemener, siffror och bindestreck. Inga å, ä, ö.
* __Content:__ Själva innehållet
* __Type:__ Vad för typ innehållet ska vara, i det här fallet ska det vara page. Kan även vara post om det är en bloggpost.
* __Filter:__ Hur innehållet ska filtreras när det visas på sidan. Allt innehåll sparas ofiltrerat i databasen och när det skrivs ut formatteras det enligt angivet filter.

Följande filter finns tillgängliga:

* htmlpurify
* bbcode
* plain
* make_clickable
* markdown
* markdownextra
* smartypants
* typographer

Vad dessa innebär i praktiken beskrivs i exempelinnehållet som läggs in i databasen när ramverket installaras.