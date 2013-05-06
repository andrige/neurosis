<h1>Blog</h1>
<<<<<<< HEAD
<p>A blog-like list of all content with the type "post".</p>
=======
<p>A blog-like list of all content with the type "post", <a href='<?=create_url("content")?>'>view all content</a>.</p>
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f

<?php if($contents != null):?>
  <?php foreach($contents as $val):?>
    <h2><?=esc($val['title'])?></h2>
    <p class='smaller-text'><em>Posted on <?=$val['created']?> by <?=$val['owner']?></em></p>
    <p><?=filter_data($val['data'], $val['filter'])?></p>
    <p class='smaller-text silent'><a href='<?=create_url("content/edit/{$val['id']}")?>'>edit</a></p>
  <?php endforeach; ?>
<?php else:?>
  <p>No posts exists.</p>
<?php endif;?>