<?php
/*==========================================================================
  
  Functions
  
========================================================================*/

/*--------------------------------------------------------------------------
  
  Helpers for the template file*/


/**
 * Add static entries in the template file.
 * The '$ne' is a global access to 'CNeurosis.php'
 */ 
$ne->data['header'] = "<h1>Neurosis</h1>";
$ne->data['slogan'] = 'A PHP-based MVC-inspired CMF';
$ne->data['favicon'] = theme_url('logo_80x80.png');
$ne->data['logo'] = theme_url('logo_80x80.png');
$ne->data['logo_width'] = 80;
$ne->data['logo_height'] = 80;
$ne->data['footer'] = <<<EOD
<p>Footer: &copy; Neurosis by Markus Lundberg, a framework modified on the Lydia framework by Mikael Roos (mos@dbwebb.se)</p>
EOD;
?>