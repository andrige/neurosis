<h1>Module Manager</h1>


<h2>About</h2>
<p><strong>Module Manager</strong> displays information on modules (controllers/methods) and enable managing of all Neurosis modules. Neurosis is made up of modules. Each module has its own subdirectory in the <code>/src/</code>-directory.</p>

<h2>Manage Neurosis modules</h2>
<p>Neurosis needs to be initialized on your server by creating databases and other necessary things. You can make a module accessible by Neurosis by abiding the <a href="http://php.net/manual/en/language.oop5.interfaces.php">interface</a> <code>IModule</code> on your modules. This means you must define some required functions, you can look at <code>CMUser::Manage/Install()</code> for how this works. You can perform the following actions through this interface as follows:</p>

<ul>
  <li><a href='<?=create_url('modules/install')?>'>install</a></li>
</ul>

<h2>Enabled controllers</h2>
<p>The controllers make up the public API of this website. Here is a list of the enabled
controllers and their methods. You enable and disable controllers in
<code>/site/config.php</code>.</p>

<ul>
<?php foreach($controllers as $key => $val): ?>
  <li><a href='<?=create_url($key)?>' title='Controller'><?=$key?></a></li>

  <?php if(!empty($val)): ?>
  <ul>
  <?php foreach($val as $method): ?>
    <li><a href='<?=create_url($key, $method)?>' title='Method'><?=$method?></a></li>
  <?php endforeach; ?>   
  </ul>
  <?php endif; ?>
 
<?php endforeach; ?>   
</ul>

