<?php
/**=========================================================================
 * 
 * A wrapper for HTMLPurifier by Edward Z. Yang, http://htmlpurifier.org/
 * http://dbwebb.se/forum/viewtopic.php?p=1467#p1467
 * The purpose of this library is to remove malicious code (so called XSS) from entering your website.
 * It has a list of other useful features as well, such as autoclosing of tags (well, you should be fixing this yourself though).
 *
 *==========================================================================
 * 
 * @package NeurosisCore
 */
class CHTMLPurifier {

  /**-------------------------------------------------------------------------
   * Properties
   *--------------------------------------------------------------------------
   */
  public static $instance = null;


  /**-------------------------------------------------------------------------
   * Purify it (THE POWER OF CHRIST...). Create an instance of HTMLPurifier if it does not exists.
   *--------------------------------------------------------------------------
   *
   * @param $text string the dirty HTML.
   * @returns string as the clean HTML.
   */
   public static function Purify($text) {   
    if(!self::$instance) {
      require_once(__DIR__.'/htmlpurifier-4.5.0-standalone/HTMLPurifier.standalone.php');
      $config = HTMLPurifier_Config::createDefault();
      $config->set('Cache.DefinitionImpl', null);
      self::$instance = new HTMLPurifier($config);
    }
    return self::$instance->purify($text);
  }
 
 
}