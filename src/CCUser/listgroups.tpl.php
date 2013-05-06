<h1>Group Manager</h1>
<p><center>"You're an admin, Harry!"</center></p>
<p>Lets you set user permissions, groups and so on.</p>


<?php if($is_authenticated && $hasroleadmin): ?>
<ul><li><a href='<?=create_url('user/creategroup/')?>'>Create new group</a></li></ul>
<div style='background-color:#f6f6f6;border:1px solid #ccc;margin-bottom:1em;padding:1em;'>
<table>
  <tr><th>Name</th><th>Acronym</th><th>Created</th><th>Action</th></tr>
<?php foreach($groups as $val):?>
  <tr><td><?=htmlent($val['name'])?></td>
      <td><?=htmlent($val['acronym'])?></td>
      <td><?=htmlent($val['created'])?></td>
      <td><a href='<?=create_url('user/managegroup/'.htmlent($val['id']))?>'>Edit</a>
          <a href='<?=create_url('user/dodeletegroup/'.htmlent($val['id']))?>'>Delete</a>
      </td>
  </tr>
<?php endforeach;?>
</div>
</table>
<?php else: ?>

<p>You do not have the necessary privileges to modify groups.</p>
<?php endif; ?>