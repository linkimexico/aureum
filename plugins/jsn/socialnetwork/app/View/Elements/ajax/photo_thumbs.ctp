<?php if (!empty($photos)): ?>
    
<?php foreach ($photos as $p): ?>
<li style="background-image:url(<?php echo $this->request->webroot?><?php echo $p['Photo']['thumb']?>);" id="photo_thumb_<?php echo $p['Photo']['id']?>">
   <a href="javascript:void(0)" onclick="showPhoto(<?php echo $p['Photo']['id']?>)"></a>    
</li>
<?php endforeach; ?>

<?php if (count($photos) >= RESULTS_LIMIT):?>
<a id="photo_load_btn" href="javascript:void(0)" onclick="loadMoreThumbs()"><i class="icon-ellipsis-horizontal icon-2x"></i></a>
<?php endif; ?>

<?php endif; ?>