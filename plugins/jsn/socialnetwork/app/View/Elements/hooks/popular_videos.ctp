<div class="box2">
    <h3><?php echo __('Popular Videos')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/videos_block', array('videos' => $popular)); ?>
    </div>
</div>