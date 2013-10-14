<?php if(isset($content['id'])):?>
  <h1><?=esc($content['title'])?></h1>
  <p><?=$content->GetFilteredData()?></p>
  <p class='smaller-text silent'><a href='<?=create_url("content/edit/{$content['id']}")?>'>edit</a> <a href='<?=create_url("page")?>'>view all</a></p>

<?php elseif(isset($contents) && $contents != null):?>
<h1>View all pages</h1>
  <ul>
  <?php foreach($contents as $val):?>
    <li><?=$val['id']?>, <a href="page/view/<?=$val['id']?>"><?=$val['title']?></a> by <?=$val['owner']?> <a href='<?=create_url("content/edit/{$val['id']}")?>'>edit</a>
  <?php endforeach; ?>
  </ul>

<?php else:?>
  <p>404: No such page exists.</p>
<?php endif;?>