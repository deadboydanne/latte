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

För att installera Latte laddar du ner hela ramverket från GitHub och laddar upp på din server. Öppna upp en webbläsare och navigera till katalogen där du installerade. Du får nu upp en välkomstsida med en länk till installations-kontrollern. Här görs en kontroll som säkerställer att du har rätt version av PHP installerad och att katalogen site/data är skrivbar. Om allt är okej får du upp ett formulär där du fyller i dina uppgifter till MySQL.

Det går också att göra en manuell installation genom att skriva in följande information i en php-fil och lägga den i mappen site/data med namnet _dbconfig.php_
    <?php
    $host = 'databas-server';
    $dbname = 'namn på databasen';
    $user = 'användarnamn';
    $pass = 'lösenord';
    ?>