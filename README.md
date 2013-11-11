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

I index-kontrollern listas alla kontrollers och deras metoder i vänsterspalten. Det finns också en inbyggd dokumentation i ramverket som ligger i kontroller `module`. Här listas alla moduler i vänsterspalten, klicka på respektive modul för att läsa mer om den.

Här följer lite exempel på vad du kan göra:

### Ändra logo, webbplatsens titel, footer och navigeringsmeny ###

Latte är enkelt att anpassa precis som man vill ha det. Du kan skapa egna teman som baseras på ett grundtema, till exempel Twitter Bootstrap. Detta gör du genom att skapa en ny mapp i __site/themes__ med namnet på temat. Här lägger du css-filer och bilder. Du kan också lägga in en egen version av temats template-fil. Detta gör du genom att kopiera index.tpl.php från temat du vill utöka och klistrar in den i ditt egna tema. Det rekommenderas dock att du inte gör några förändringar här om du inte vet hur temat är uppbyggt eftersom du då kan få konstiga fel.

I __config.php__ gör du alla inställningar som har med temat att göra. Arrayen `$lt->config['theme']` håller reda på alla inställningar. Så här ser det ut i config.php:

	/**
	 * Settings for the theme.
	 */
	$lt->config['theme'] = array(
	  // The name of the theme in the theme directory
	  'path'    => 'site/themes/cloudtheme',
	  'parent'          => 'themes/bootstrap',
	  'name'    => 'bootstrap', 
	  'stylesheet'  => 'style.css',
	  'template_file'   => 'index.tpl.php',
	  'menu_to_region' => array('fresh-install'=>'navbar'),
	// Add static entries for use in the template file. 
	  'data' => array(
	    'header' => 'Latte',
	    'slogan' => 'A PHP-based MVC-inspired CMF',
	    'favicon' => 'logo_80x80.png',
	    'logo' => 'logo_80x80.png',
	    'logo_width'  => 80,
	    'logo_height' => 80,
	    'footer' => '<p>Latte &copy; by Andreas Carlsson (andreasc89@gmail.com)</p>',
	  ),
	);

* __$lt->config['theme']['path']__ innehåller sökvägen till ditt valda tema.
* __$lt->config['theme']['parent']__ är sökvägen till temat som ditt egna tema ärver ifrån.
* __$lt->config['theme']['name']__ är namnet på ditt föräldratema.
* __$lt->config['theme']['style']__ namnet på temats stilmall.
* __$lt->config['theme']['template_file']__ mallsidan, i de flesta fall index.tpl.php. Vill du använda någon annat på din mallsida anger du det här.
* __$lt->config['theme']['menu_to_region']__ vilken meny som ska kopplas till regionen __navbar__.
* __$lt->config['theme']['data']['header']__ rubriken på sidan.
* __$lt->config['theme']['data']['slogan']__ sidans slogan, kan användas av vissa teman.
* __$lt->config['theme']['data']['favicon']__ namn på sidans favicon, i det här fallet används samma fil som till logotypen.
* __$lt->config['theme']['data']['logo']__ sidans logotyp. Vill du byta ut så lägg din fil i site/themes/ditt valda tema och ange namnet här.
* __$lt->config['theme']['data']['logo_width']__ bredd på logotypen.
* __$lt->config['theme']['data']['logo_height']__ höjd på logotypen.
* __$lt->config['theme']['data']['footer']__ texten som visas längst ner på sidan.


### Skapa en blogg ###

När du installerar ramverket finns redan en exempel-kontroller för blog, gästbok och sida med i paketet. Den ligger i mappen __site/src/CCMycontroller__. Jag tänkte även visa hur du skapar en ny kontroller för en blogg. Börja med att göra en mapp i __site/src__, vi kan kalla den för CCMyBlog. I mappen gör skapar du en phpfil med samma namn och skriver följande:

	<?php
	/**
	 * Sample controller for creating a blog.
	 */
	class CCMyBlog extends CObject implements IController {
	
	  /**
	   * Constructor
	   */
	  public function __construct() { parent::__construct(); }
	
	  /**
	   * The is an example blog
	   */
	  public function Index() {
	    $content = new CMContent();
	    $this->views->SetTitle('My blog'.htmlEnt($content['title']))
	                ->AddInclude(__DIR__ . '/blog.tpl.php', array(
	                  'contents' => $content->ListAll(array('type'=>'post', 'order-by'=>'title', 'order-order'=>'DESC')),
	                ));
	  }
	}

Gör en ny fil som du kallar för __blog.tpl.php__, detta är vyn som visar själva bloggen. Den filen kan se ut till exempel så här:

	<h1>My beautiful blog</h1>
	<p>This is my example blog built using Latte.</p>
	
	<?php if($contents != null):?>
	  <?php foreach($contents as $val):?>
	    <h2><?=esc($val['title'])?></h2>
	    <p class='smaller-text'><em>Posted on <?=$val['created']?> by <?=$val['owner']?></em></p>
	    <p><?=filter_data($val['data'], $val['filter'])?></p>
	    <p class='smaller-text silent'><a href='<?=create_url("content/edit/{$val['id']}")?>'>edit</a></p>
	  <?php endforeach; ?>
	<?php else:?>
	  <p>No posts exists.</p>
	<?php endif;?>

För att bloggen ska gå att nå måste du lägga till den i `$lt->config['controllers']`. Gör en ny rad och lägg till följande i arrayen:

	'myblog' => array('enabled' => true,'class' => 'CCMyBlog'),

Färdigt! Gå till installation av Latte och skriv /myblog på slutet så kommer ska du se din färdiga blogg.


### Skapa en sida ###

En sida skapar du enkelt genom att gå till kontrollern `content/create`. Det finns även en länk högst upp på sidan som går direkt till `content`-kontrollern. Här fyller du i titel, länktext, innehåll, typ och filter. En kort förklaring till vad dessa innebär:

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