<?php
/*==========================================================================
  
  (C) 2013 by Markus Lundberg
  
  Controller for development and testing purpose, helpful methods for the developer.
  
  ========================================================================*/
/**
 * @package NeurosisCore
 */
class CCReport implements IController {

  /*==========================================================================
    
    Implementing interface IController. All controllers must have an index action. 
    
    ========================================================================*/
public function Index() {  
  $ne = CNeurosis::Instance();
  $ne->data['title'] = "Redovisning av moment";
  $ne->data['main'] = <<<EOD
<h2>Kmom02: Grunden till ett MVC-ramverk </h2>
  <p><strong>Vilket läsmaterial valde du att studera för formulärhantering och vad tyckte du om det?</strong><br/>
    </p>
  <p><strong>Hur kändes det att jobba med CForm-klassen?</strong><br/>
    </p>
  <p><strong>Vilken strategi valde du för formulärhantering?</strong><br/>
    </p>
  <p><strong>Hur valde du att lagra lösenordet?</strong><br/>
    </p>
  <p><strong>Gjorde du extrauppgifterna, kommentera hur du gjorde dem?</strong><br/>
    </p>
    Projektet kan du hitta på <a href="https://github.com/andrige/neurosis">GitHub</a> 
EOD;
  }
}