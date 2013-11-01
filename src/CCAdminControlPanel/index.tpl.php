<h1>Admin Control Panel Index</h1>

<?php if($is_authenticated && $user['hasRoleAdmin']): ?>
<p>Welcome to the Admin Control Panel, choose action in the sidebar.</p>
<?php elseif($is_authenticated): ?>
<p>Access denied: You do not belong to the Admin-group.</p>
  
<?php else: ?>

<p>Access denied: You need to be logged in to use the Admin Control Panel.</p>

<?php endif; ?>