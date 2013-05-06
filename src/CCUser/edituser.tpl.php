<h1>User Profile</h1>
<p>You can view and update user's profile information.</p>

<?php if($is_authenticated): ?>
<?=$profile_form?>
<p>User created at <?=$user[0]['created']?> and last updated at <?=$user[0]['updated']?>.</p>
<?php if(isset($memberships) && $memberships != null): ?>
<h2>Member of group(s):</h2>
<ul>
<?php foreach($memberships as $group): ?>
<li><?=$group['name']?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php else: ?>
<p>User is anonymous and not authenticated.</p>
<?php endif; ?>