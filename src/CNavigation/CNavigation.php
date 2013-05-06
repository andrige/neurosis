<?php
/** ========================================================================
 * 
 * Navigation and menu
 *
 *==========================================================================
 * 
 * 
*/
class CNavigation extends CObject {
  /** ------------------------------------------------------------------------
   * Properties
   *--------------------------------------------------------------------------
  */
  
  /** ------------------------------------------------------------------------
   * Construct
   *--------------------------------------------------------------------------
  */
  public function __construct($ne=null) { parent::__construct($ne); }
  
  /**-------------------------------------------------------------------------
   * Draw HTML for a menu defined in $ne->config['menus']
   *--------------------------------------------------------------------------
   *
   * @param $menu string then key to the menu in the config-array.
   * @returns string with the HTML representing the menu.
   */
  public function DrawMenu($menu) {
    $items = null;
    if(isset($this->config['menus'][$menu])) {
      $requiresAdmin = $this->config['menus'][$menu]['reqRoleAdmin'];
      $isAdmin = $this->user['hasRoleAdmin'];
      if($requiresAdmin && $isAdmin || !$requiresAdmin) {    // E.g. config['menus']['site-navbar'], contains a list of menu items.
        foreach($this->config['menus'][$menu]['items'] as $val) {
          $selected = null;
          if($val['url'] == $this->request->request || $val['url'] == $this->request->routed_from) {
            $selected = " class='selected'";
          }
          $items .= "<li><a {$selected} href='" . $this->CreateUrl($val['url']) . "'>{$val['label']}</a></li>\n";
        }
      }
    } else {
      throw new Exception('No such menu.');
    }
    return "<ul class='menu {$menu}'>\n{$items}</ul>\n";
  }
  
  /** ------------------------------------------------------------------------
   * Temporarily override default navbar menu
   *--------------------------------------------------------------------------
   * @param string Name of menu you want to override for this page session
  */
  public function OverrideNavbar($menu=null) {
    if(isset($menu['reqRoleAdmin']) && !$menu['reqRoleAdmin']) { return;
    } else {
      $this->config['theme']['menu_to_region'] = array($menu=>'navbar');
    }
  }
}