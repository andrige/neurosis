<?php
/**
* Standard controller layout.
*
* @package NeurosisCore
*/
class CCIndex implements IController {

   /**
    * Implementing interface IController. All controllers must have an index action.
    */
   public function Index() {   
      global $ne;
      $ne->data['title'] = "The Index Controller";
      $ne->data['main'] = "This is Neurosis. It aptly stands for how I feel right now. It's a framework modified on the Lydia framework by Michael Roos for the course phpmvc (<a href='http://www.dbwebb.se/kurser'>dbwebb.se</a> for more information in swedish).";
   }
}