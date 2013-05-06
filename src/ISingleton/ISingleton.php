<?php
/**
* Interface for classes implementing the singleton pattern.
*
* @package NeurosisCore
*/

/**
* A class set as an 'interface' is used as a template for other classes.
* We can specify the methods the children must have, or else fatal error.
* Set up for a streamlined structure that we must obey.
*/
interface ISingleton {
   public static function Instance();
}