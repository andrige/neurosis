<h1>Group Profile</h1>
<p>You can view and update group information and memberships.</p>

<?php if($is_authenticated): ?>
  <?=$profile_form?>
  <?php if(isset($members) && !empty($members)): ?>
  <h2>Group members:</h2>
  <div style='background-color:#f6f6f6;border:1px solid #ccc;margin-bottom:1em;padding:1em;'>
  <table>
  <tr><th>Name</th><th>Email</th><th>Acronym</th><th>Created</th><th>Action</th></tr>
<?php foreach($members as $member):?>
  <tr><td><a href='<?=create_url('user/manageuser/'.htmlent($member['id']))?>'><?=htmlent($member['name'])?></a></td>
      <td><?=htmlent($member['email'])?></td>
      <td><?=htmlent($member['acronym'])?></td>
      <td><?=htmlent($member['created'])?></td>
      <td><a href='<?=create_url('user/manageuser/'.htmlent($member['id']))?>'>Edit</a>
          <a href='<?=create_url('user','doremoveuserfromgroup',$group_id.'/'.$member['id'])?>'>Remove</a>
      </td>
  </tr>
<?php endforeach;?>
</div>
</table>

  <?php else: ?>
    <p>Group does not have any members assigned yet.</p>
  <?php endif; ?>

<?php else: ?>
<p>User is anonymous and not authenticated.</p>
<?php endif; ?>

