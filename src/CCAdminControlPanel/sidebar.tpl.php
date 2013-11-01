<?php if($is_authenticated && $user['hasRoleAdmin']): ?>
  <ul>
  <li><a href="<?=create_url('acp/users')?>">Manage users</a></li>
  <li><a href="<?=create_url('acp/groups')?>">Manage groups</a></li>
  <li><a href="<?=create_url('acp/content')?>">Manage content</a></li>
  </ul>
<?php endif; ?>