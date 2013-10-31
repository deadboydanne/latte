<?php
/**
* A model for content stored in database.
* 
* @package LatteCore
*/
class CMContent extends CObject implements IHasSQL, ArrayAccess, IModule {

  /**
   * Properties
   */
  public $data;
  


  /**
   * Constructor
   */
  public function __construct($id=null) {
    parent::__construct();
    if($id) {
      $this->LoadById($id);
    } else {
      $this->data = array();
    }
  }


  /**
   * Implementing ArrayAccess for $this->data
   */
  public function offsetSet($offset, $value) { if (is_null($offset)) { $this->data[] = $value; } else { $this->data[$offset] = $value; }}
  public function offsetExists($offset) { return isset($this->data[$offset]); }
  public function offsetUnset($offset) { unset($this->data[$offset]); }
  public function offsetGet($offset) { return isset($this->data[$offset]) ? $this->data[$offset] : null; }




  /**
   * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
   *
   * @param string $key the string that is the key of the wanted SQL-entry in the array.
   */
  public static function SQL($key=null) {
    $order_order  = isset($args['order-order']) ? $args['order-order'] : 'ASC';
    $order_by     = isset($args['order-by'])    ? $args['order-by'] : 'id';  
    $queries = array(
      'drop table content'      => "DROP TABLE IF EXISTS Content;",
      'create table content'    => "CREATE TABLE IF NOT EXISTS Content(id INT(11) PRIMARY KEY AUTO_INCREMENT, filter VARCHAR(20), linktext VARCHAR(100), type VARCHAR(20), title VARCHAR(100), data TEXT, idUser INT(11), created DATETIME, updated DATETIME, deleted DATETIME, FOREIGN KEY(idUser) REFERENCES User(id));",
      'insert content'          => 'INSERT INTO Content (filter,linktext,type,title,data,idUser,created) VALUES (?,?,?,?,?,?,?);',
      'select * by id'          => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.id=?;',
      'select * by key'         => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.linktext=?;',
      'select * by type'        => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE type=? ORDER BY '.$order_by.' '.$order_order.';',
      'select *'                => 'SELECT c.*, u.username as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id;',
      'update content'          => "UPDATE Content SET filter=?, linktext=?, type=?, title=?, data=?, updated=? WHERE id=?;",
     );
    if(!isset($queries[$key])) {
      throw new Exception("No such SQL query, key '$key' was not found.");
    }
    return $queries[$key];
  }


  /**
   * Filter content according to a filter.
   *
   * @param $data string of text to filter and format according its filter settings.
   * @returns string with the filtered data.
   */
  public static function Filter($data, $filter) {
	$accepted_filters = array('htmlpurify','bbcode','plain','make_clickable','markdown','markdownextra','smartypants','typographer');
	if(in_array($filter,$accepted_filters)) {
		$data = CTextFilter::filter($data,$filter);
	} else {
		$data = CTextFilter::filter($data,"plain");
	}
    return $data;
  }

  /**
   * Get the filtered content.
   *
   * @returns string with the filtered data.
   */
  public function GetFilteredData() {
    return $this->Filter($this['data'], $this['filter']);
  }


  /**
   * Implementing interface IModule. Manage install/update/deinstall and equal actions.
   */
  public function Manage($action=null) {
	if($this->user['id'] == 0) {
		$user_id = 1;
	} else {
		$user_id = $this->user['id'];
	}
    switch($action) {
      case 'install': 
        try {
	      $this->db->ExecuteQuery(self::SQL('drop table content'));
	      $this->db->ExecuteQuery(self::SQL('create table content'));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','om-ramverket', 'page', 'Om ramverket', 'Här står det lite roliga saker om mitt ramverk Latte. Den här texten är filtrerad med filtret plain. Inga <b>html</b>-taggar eller [b]BB-code[/b]-taggar är tillåtna. Däremot formateras länkar automatiskt så att det blir klickbara. Till exempel http://www.dbwebb.se', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('htmlpurify','kontakt', 'page', 'Kontakt', 'Detta skulle kunna vara en kontaktsida. Den här texten är filtrerad med HTML-purify som godkänner vissa html taggar. Till exempel <b>fetstil</b>, <i>kursiv,</i> och <u>understruken</u> text. Däremot filtreras <javascript>javascript</javascript> bort', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('plain','filter-plain', 'post', 'Filter: Plain', 'Den här texten filtreras med plain som inte tillåter <b>html-taggar</b> eller [b]bb-code[/b]-taggar. Däremot körs metoden make_clickable() som gör länkar klickbara. Till exempel http://www.dbwebb.se', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('htmlpurify','filter-htmlpurify', 'post', 'Filter: HTML Purify', 'Den här texten är körd genom filtret htmlpurify som <b>tillåter vissa</b> <i>HTML</i>-länkar. Däremot inte någon [b]bb-code[/b] eller <?php ?> php-kod.', $user_id, date('Y-m-d H:i:s')));
	      $this->db->ExecuteQuery(self::SQL('insert content'), array('make_clickable','filter-make-clickable', 'post', 'Filter: Make clickable', 'Om man bara använder filtret make_clickable så tillåts ingen html-kod eller bb-kod. Däremot görs länkar som till exempel http://www.dbwebb.se automatiskt klickbara. Skillnaden mot filtret plain är att inga radbrytningar görs när man använder make_clickable.', $user_id, date('Y-m-d H:i:s')));
		  $this->db->ExecuteQuery(self::SQL('insert content'), array('bbcode','filter-bbcode', 'post', 'Filter: BB Code', 'Med filtret BB code kan man använda [b]BB-code[/b] syntaxen för att [i]formatera[/i] texten. Radbrytningar läggs in automatiskt, däremot körs inte make_clickable så http://www.dbwebb.se kommer bara att skrivas ut som text. För att få en länk använder man BB-code syntaxen URL: [url]http://www.dbwebb.se[/url].', $user_id, date('Y-m-d H:i:s')));
		  $this->db->ExecuteQuery(self::SQL('insert content'), array('markdown','filter-markdown', 'post', 'Filter: Markdown', "Markdown är ett sätt att formatera text på som gör att man kan skriva text som är läsbar oavsett om den är **formaterad** eller om man läser den *oformaterad*. Man kan till exempel göra\n###Rubriker\n och\n\n1. Listor\n2. som är\n3. numrerade\n\n* Eller\n* Onumrerade.\n\nOm jag försöker göra en tabell här kommer den inte att formateras på rätt sätt.\n###Tabeller\n\n| Klockan  | Måltid        |\n|----------|:--------------|\n| Kl. 6    | Vakna!        |\n| Kl. 7    | Äta frukost   |\n| Kl. 12   | Äta lunch     |\n| Kl. 18   | Äta middag    |\n| Kl. 21   | Äta kvällsmat |\n\n", $user_id, date('Y-m-d H:i:s')));
		  $this->db->ExecuteQuery(self::SQL('insert content'), array('markdownextra','filter-markdown-extra', 'post', 'Filter: Markdown Extra', "Med Markdown Extra kan man göra **allt som går** att göra med Markdown plus lite mera. Till exempel\n###Tabeller\n\n| Klockan  | Måltid        |\n|----------|:--------------|\n| Kl. 6    | Vakna!        |\n| Kl. 7    | Äta frukost   |\n| Kl. 12   | Äta lunch     |\n| Kl. 18   | Äta middag    |\n| Kl. 21   | Äta kvällsmat |\n\n", $user_id, date('Y-m-d H:i:s')));
		  $this->db->ExecuteQuery(self::SQL('insert content'), array('smartypants','filter-smartypants', 'post', 'Filter: Smartypants', 'Smartypants är ett filter som snyggar till typografin. Till exempel blir "citat" mycket snyggare och man kan göra --Tankestreck. Till skillnad från Typographer lägger Smartypants in till hårda mellanslag vid långa tal, till exempel 100 000 000 och dessa kan därför radbrytas', $user_id, date('Y-m-d H:i:s')));
		  $this->db->ExecuteQuery(self::SQL('insert content'), array('typographer','filter-typographer', 'post', 'Filter: Typographer', 'Typographer utökar smartypants och lägger till "hårda" mellanslag för kolon : utropstecken ! och frågetecken ?. Typographer lägger också till hårda mellanslag vid långa talföljder som har tusen-separatorer, till exempel 100 000 000 000 000.', $user_id, date('Y-m-d H:i:s')));
		  $this->db->ExecuteQuery(self::SQL('insert content'), array('typographer','about-me', 'post', 'About me', 'This is an about me-page with some funny information about me. Enjoy!', $user_id, date('Y-m-d H:i:s')));
	      return array('success', 'Successfully created the database tables and created some default blog posts and pages.');
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][$this->config['database']['type']]['dsn']);
        }
      break;
      
      default:
        throw new Exception('Unsupported action for this module.');
      break;
    }
  }


  /**
   * Save content. If it has a id, use it to update current entry or else insert new entry.
   *
   * @returns boolean true if success else false.
   */
  public function Save() {
    $msg = null;
    if($this['id']) {
      $this->db->ExecuteQuery(self::SQL('update content'), array($this['filter'],$this['linktext'], $this['type'], $this['title'], $this['data'], date('Y-m-d H:i:s'), $this['id']));
      $msg = 'updated';
    } else {
      $this->db->ExecuteQuery(self::SQL('insert content'), array($this['filter'],$this['linktext'], $this['type'], $this['title'], $this['data'], $this->user['id'], date('Y-m-d H:i:s')));
      $this['id'] = $this->db->LastInsertId();
      $msg = 'created';
    }
    $rowcount = $this->db->RowCount();
    if($rowcount) {
      $this->AddMessage('success', "Successfully {$msg} content '" . htmlEnt($this['linktext']) . "'.");
    } else {
      $this->AddMessage('error', "Failed to {$msg} content '" . htmlEnt($this['linktext']) . "'.");
    }
    return $rowcount === 1;
  }
    

  /**
   * Load content by id.
   *
   * @param id integer the id of the content.
   * @returns boolean true if success else false.
   */
  public function LoadById($id) {
    $res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by id'), array($id));
    if(empty($res)) {
      $this->AddMessage('error', "Failed to load content with id '$id'.");
      return false;
    } else {
      $this->data = $res[0];
    }
    return true;
  }
  
  
  /**
   * List all content.
   *
   * @param $args array with various settings for the request. Default is null.
   * @returns array with listing or null if empty.
   */
  public function ListAll($args=null) {    
    try {
      if(isset($args) && isset($args['type'])) {
        return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by type', $args), array($args['type']));
      } else {
        return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select *', $args));
      }
    } catch(Exception $e) {
      echo $e;
      return null;
    }
  }

  
  
}