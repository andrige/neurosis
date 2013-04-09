<?php
/*==========================================================================
  
  Helpers for theming, available for all themes in their template files and functions.php.
  This file is included right before the themes own functions.php
  
  ========================================================================*/
/**
* Create a url by prepending the base_url.
*
* Gets the instance (TBA why not '$ne' though?) -> goes to the request class (CRequest.php) with the 
*-method 'base_url' in it and returns the base url -> appends a trimmed (whitespace from beginning and end removed) and adds '/'.
*/


/*--------------------------------------------------------------------------
  
  Print debuginformation from the framework*/
  
function get_debug() {
  $ne = CNeurosis::Instance();
  $html = "<h2>Debuginformation</h2><hr><p>The content of the config array:</p><pre>" . htmlentities(print_r($ne->config, true)) . "</pre>";
  $html .= "<hr><p>The content of the data array:</p><pre>" . htmlentities(print_r($ne->data, true)) . "</pre>";
  $html .= "<hr><p>The content of the request array:</p><pre>" . htmlentities(print_r($ne->request, true)) . "</pre>";
  // $html .= "<p>REQUEST_URI - " . htmlentities($_SERVER['REQUEST_URI']) . "</p>";
  // $html .= "<p>SCRIPT_NAME - " . htmlentities($_SERVER['SCRIPT_NAME']) . "</p>";
  return $html;
}


function base_url($url) {
  return CNeurosis::Instance()->request->base_url . trim($url, '/');
}

/**
* Return the current url.
*/
function current_url() {
  return CNeurosis::Instance()->request->current_url;
}