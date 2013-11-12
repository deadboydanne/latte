<h1>Gallery Controller Index</h1>
<p>One controller to manage the gallery.</p>

<h2>All content</h2>
<?php if($gallerycontent != null):?>
  <ul>
  <?php foreach($gallerycontent as $val):?>
    <li><img src="site/data/<?=$val['id']?>.jpg" alt="<?=$val['title']?>"><br><?=$val['id']?>, <?=$val['title']?> by <?=$val['owner']?> <a href='<?=create_url("gallery/edit/{$val['id']}")?>'>edit</a> <a href='<?=create_url("gallery/view/{$val['id']}")?>'>view</a>
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