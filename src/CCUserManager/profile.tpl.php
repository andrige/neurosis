<h1>User Profile</h1>
<p>You are now viewing NAME's profile.</p>

<?php if($is_authenticated): ?>
<?=$profile_form?>
<p>You were created at <?=$user['created']?> and last updated at <?=$user['updated']?>.</p>
<p>You are member of <?=count($user['groups'])?> group(s).</p>
<ul>
<?php foreach($user['groups'] as $group): ?>
<li><?=$group['name']?>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>User is anonymous and not authenticated.</p>
<?php endif; ?>