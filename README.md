
NEUROSIS : CMF BASED ON THE LYDIA FRAMEWORK BY MIKAEL ROOS
MODIFIED BY MARKUS LUNDBERG (www.andrige.com)
Created for learning purposes, 2013-04 to 2013-05

 Installation
--------------------------------

1. Clone the framework using GitBash:

  a. Browse to the folder you wish to clone your project into, then use the clone-command.
     Here are the relevant commands:
  
    * ls                                              | Lists all objects/sbufolders in folder
    * cd folder                                       | Enter folder with name 'folder'
    * git clone git:github.com/andrige/neurosis.git   | Clone framework into folder
  
2. Move Neurosis in its entirety to your webserver.
      
3. Set your access modifier (CHMOD) to 777 on the folder 'data' at 'site/data'.
    This is required to enable database (.ht.sqlite) to be created and written.
    
4. You might need to change the file '.htaccess' in the root-folder and change the
    line after "RewriteBase" to what is the real adress to your files, or to comment it 
    out by adding "#" at the start of the line. Some webhosts may rewrite the base of 
    the adress and thus you must adjust so Neurosis can still find the right location.
    
5. Load website. It will redirect you to a install-page with a link. Press it.

6. Link will create database entries as well as example posts and users. 
    You'll be logged in as user 'root'. You can login to root again under username
    'root' and password 'root'.
    
7. Enjoy! This is a very modest framework, but you can now moderate webpage and 
    begin to create content, moderate users and groups using the admin functions 
    available to you as admin.

    
 Modify theme and add blog, page
--------------------------------

1. In the 'config.php' file in the 'site/' folder you'll find many useful fields to modify your theme:

  a. Enable debug output texts. E.g. set "$ne->config['debug']['display-neurosis'] = false;" to true.
    
  b. Create new menus. 
     Look for '$ne->config['menus'] = array(' and the list beneath. To get started you can
     try adding/changing entries under the 'my-navbar'. Try adding this text:
     'login' => array('label'=>'Login','url'=>'user/login'),
        
  c. Set logotype url, footer text, header title, the content of navigation menus.
     You'll find these settings at the bottom of 'config.php'.
      
  d. You can change logo image by replacing the 'logo.png' in 'site/themes/neat' folder.

2. You can modify the existing theme by editing the file 'style.css' at 'site/themes/style.css'.

3. You can change the look of the parent theme, 'grid', by using the admin functions.
   These changes to the parent theme will carry over to your sub-theme which you can
   find in the 'site/' folder. Be sure to set the folder 'themes/grid/' to CHMOD 777, and be 
   wary that it's not entirely sure what causes it to work or not. One way is to try 
   deleting the 'style.css' file and let the framework recompile a new one for you.