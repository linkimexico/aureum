<ul class="list6 list6sm">
<?php
foreach ($site_themes as $theme_id => $theme):
    if ( $theme_id != 'mobile' ):
?>
	<li><a href="<?php echo $this->request->base?>/home/do_theme/<?php echo $theme_id?>"><?php echo $theme?></a></li>
<?php
    endif;
endforeach;
?>
</ul>