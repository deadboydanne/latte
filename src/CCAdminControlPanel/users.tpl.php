<h1>User Profiles</h1>
<p>You can view and update all user profiles in the database.</p>

<?php if($is_authenticated && $user['hasRoleAdmin']): ?>
  <ul>
  <?php foreach($allusers as $user): ?>
    <li><a href="<?=create_url('acp/users/'.$user['id'])?>"><?=$user['name']?></a> (<?=$user['username']?>)
  <?php endforeach; ?>
  </ul>
  	<hr>
  <ul>
  	<li><a href="<?=create_url('acp/create')?>">Create new user</a></li>
  </ul>
<?php else: ?>
  <p>Access denied.</p>
<?php endif; ?>