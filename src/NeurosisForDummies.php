/**=========================================================================
 * 
 * This is a document not intended to be run as part of website.
 * Only shorthand notes about classes.
 *
 *==========================================================================
 * 
 * 
 */
 
 '$this->' is accessible in classes inheriting CObject or CNeurosis itself.
 
 These are newed in CNeurosis.php:    
 ->config->   = config.php                    Global site settings.
 ->request->  = CRequest.php                  Url requests, fetches controllers/methods/arguments and process it.
 ->data->     = 
 ->db->       = CMDatabase.php        MODEL | Database handling, mostly wrappers (methods we've written which adds extra functionality) for standard SQL methods.
 ->user->     = CMUser.php            MODEL | Interacts with database on user things. Sets group and site access levels. Modifies the $user var created in Neurosis.
 ->views->    = CViewContainer.php    MODEL | Grabs views requested elsewhere and extracts variables sent along with that request for use in the view.
 ->session->  = CSession.php                | Handles $_SESSION. Grabs data from $_SESSION, makes it possible to have 'keys' of a session (['ASession][0]) and flash-messages stored between page loads.
 
 As of v206 we've started using ArrayAccess in the declaration of our classes.
 What this means is that we'll access other classes methods as an array. Thus, "CCIndex->Index" becomes "CCIndex['Index']".
 
    public $config = array();              // Config file which stores site pages(controllers), debugging and other guff.
    public $request;                       // Handles the url requests (gets controller and method).
    public $data;                          // Stores overhead data (title, time, etc) as array.
    public $db;                            // Contains the database (.sql).
    public $views;                         // Stores views as array.
    public $session;                       // Stores session data as array.
    public $timer = array();
    public $user;