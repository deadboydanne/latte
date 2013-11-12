<?php if($gallery['created']): ?>
  <h1>Edit gallery content</h1>
  <p>You can edit and save this gallery.</p>
<?php else: ?>
  <h1>Create gallery content</h1>
  <p>Upload images to gallery.</p>
<?php endif; ?>


<?=$form->GetHTML(array('class'=>'gallery-edit'))?>

<p class='smaller-text'><em>
<?php if($gallery['created']): ?>
  This gallery were created by <?=$gallery['owner']?> <?=time_diff($gallery['created'])?> ago.
<?php else: ?>
  gallery not yet created.
<?php endif; ?>

<?php if(isset($gallery['updated'])):?>
  Last updated <?=time_diff($gallery['updated'])?> ago.
<?php endif; ?>
</em></p>

<p>
<a href='<?=create_url('gallery', 'create')?>'>Create new</a>
<a href='<?=create_url('page', 'view', $gallery['id'])?>'>View</a>
<a href='<?=create_url('gallery')?>'>View all</a>
</p>