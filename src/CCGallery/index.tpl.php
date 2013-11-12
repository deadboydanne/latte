<h1>Gallery Controller Index</h1>
<p>One controller to manage the gallery.</p>

<h2>All content</h2>
<?php if($gallerycontent != null):?>
  <ul class=inline>
  <?php foreach($gallerycontent as $val):?>
    <li style="padding-bottom: 50px;"><img src="site/data/<?=$val['id']?>.jpg" alt="<?=$val['title']?>"><br><?=$val['id']?>, <?=$val['title']?>, uploaded by <?=$val['owner']?> <a href='<?=create_url("gallery/edit/{$val['id']}")?>'>edit</a>
  <?php endforeach; ?>
  </ul>
<?php else:?>
  <p>No content in gallery.</p>
<?php endif;?>

<h2>Actions</h2>
<ul>
  <li><a href='<?=create_url('gallery/manage')?>'>Init database, create gallery table</a>
  <li><a href='<?=create_url('gallery/create')?>'>Create new content</a>
</ul>