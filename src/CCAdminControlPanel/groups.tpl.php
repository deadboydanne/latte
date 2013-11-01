<h1>Manage groups</h1>
<p>Manage group names and permissions.</p>

<?php if($is_authenticated && $user['hasRoleAdmin']): ?>
  <ul>
  <?php foreach($allgroups as $group): ?>
    <li><a href="<?=create_url('acp/group/'.$group['id'])?>"><?=$group['name']?></a> (<?=$group['username']?>)
  <?php endforeach; ?>
  </ul>
  	<hr>
  <ul>
  	<li><a href="<?=create_url('acp/creategroup')?>">Create new group</a></li>
  </ul>
<?php else: ?>
  <p>Access denied.</p>
<?php endif; ?>