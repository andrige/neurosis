<h1>User Manager</h1>
<p><center>"You're an admin, Harry!"</center></p>
<p>Lets you set user permissions, groups and so on.</p>

<div style='background-color:#f6f6f6;border:1px solid #ccc;margin-bottom:1em;padding:1em;'>
<table>
  <tr><th>Name</th><th>Email</th><th>Acronym</th><th>Created</th></tr>
<?php foreach($users as $val):?>
  <tr><td><?=htmlent($val['name'])?></td><td><?=htmlent($val['email'])?></td><td><?=htmlent($val['acronym'])?></td><td><?=htmlent($val['created'])?></td><td><a href=''</td></tr>
<?php endforeach;?>
</div>
</table>