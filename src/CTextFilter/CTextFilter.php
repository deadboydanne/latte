<?php
/**
 * Class for filtering user submitted data before displaying on page
 * 
 * @package LatteCore
 */
class CTextFilter {

/**
 * Properties
 */
public static $purify = null;


public static function filter($data,$filter) {
    switch($filter) {
        case 'php': $data = nl2br(make_clickable(eval('?>'.$data))); break;
        case 'html': $data = nl2br(make_clickable($data)); break;
        case 'htmlpurify': $data = nl2br(self::purify($data)); break;
        case 'make_clickable': $data = make_clickable(htmlEnt($data)); break;
        case 'bbcode': $data = nl2br(bbcode2html(htmlEnt($data))); break;
        case 'markdown': $data = self::markdownextra($data,'Markdown_Parser'); break;
        case 'markdownextra': $data = self::markdownextra($data,'MarkdownExtra_Parser'); break;
        case 'smartypants': $data = nl2br(make_clickable(self::smartypants(htmlEnt($data),'SmartyPants_Parser'))); break;
        case 'typographer': $data = nl2br(make_clickable(self::smartypants(htmlEnt($data),'SmartyPantsTypographer_Parser'))); break;
        case 'plain':
        default: $data = nl2br(make_clickable(htmlEnt($data))); break;
    }
    return $data;
}

/**
   * Clean your HTML with HTMLPurifier, create an instance of HTMLPurifier if it does not exists. 
   *
   * @param $text string the dirty HTML.
   * @return string as the clean HTML.
   */
   private static function Purify($text) {   
    if(!self::$purify) {
      require_once(__DIR__.'/htmlpurifier-4.5.0-standalone/HTMLPurifier.standalone.php');
      $config = HTMLPurifier_Config::createDefault();
      $config->set('Cache.DefinitionImpl', null);
      self::$purify = new HTMLPurifier($config);
    }
    return self::$purify->purify($text);
}

/**
 * Format text according to Markdown och Markdown extra syntax.
 *
 * @param string $text the text that should be formatted.
 * @param string $parser the parser method, can be Markdown_Parser och MarkdownExtra_Parser.
 * @return string as the formatted html-text.
 */
private static function markdownextra($text,$parser) {
    require_once(__DIR__ . '/php-markdown-extra/markdown.php');
    return Markdown($text,$parser);
}


/**
 * Format text according to Smartypants syntax.
 *
 * @param string $text the text that should be formatted.
 * @param string $parser the parser method can be SmartyPants_Parser or SmartyPantsTypographer_Parser.
 * @return string as the formatted html-text.
 */
private static function smartypants($text,$parser) {
    require_once(__DIR__ . '/typographer/smartypants.php');
    return SmartyPants($text,$parser);
}

/**
 * Format text according to Smartypants Typographer syntax.
 *
 * @param string $text the text that should be formatted.
 * @return string as the formatted html-text.

public static function typographer($text) {
    require_once(__DIR__ . '/typographer/smartypants.php');
    return SmartyPants($text);
}
 */

/**
 * Make clickable links from URLs in text.
 *
 * @param string text text to be converted.
 * @return string the formatted text.
 */
private static function make_clickable($text) {
    return preg_replace_callback(
      '#\b(?<![href|src]=[\'"])https?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
      create_function(
        '$matches',
        'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
        ),
    $text
    );
}
  

/**
 * BBCode formatting converting to HTML.
 *
 * @param string text text to be converted.
 * @return string the formatted text.
 */
private static function bbcode2html($text) {
    $search = array( 
        '/\[b\](.*?)\[\/b\]/is', 
        '/\[i\](.*?)\[\/i\]/is', 
        '/\[u\](.*?)\[\/u\]/is', 
        '/\[img\](https?.*?)\[\/img\]/is', 
        '/\[url\](https?.*?)\[\/url\]/is', 
        '/\[url=(https?.*?)\](.*?)\[\/url\]/is' 
    );   
    $replace = array( 
        '<strong>$1</strong>', 
        '<em>$1</em>', 
        '<u>$1</u>', 
        '<img src="$1" />', 
        '<a href="$1">$1</a>', 
        '<a href="$1">$2</a>' 
    );     
    return preg_replace($search, $replace, $text);
} 

}