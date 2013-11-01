<h1>Edit user <?=$edituser['name']?></h1>

<?php if($is_authenticated && $user['hasRoleAdmin']): ?>
<p>View and update user profiles</p>
  <?=$profile_form?>
  <?php else: ?>
  <p>Access denied.</p>
<?php endif; ?>