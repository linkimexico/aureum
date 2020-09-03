<ul class="list6 list6sm">
<?php
foreach ($site_langs as $lang_key => $lang):
?>
	<li><a href="<?php echo $this->request->base?>/home/do_language/<?php echo $lang_key?>"><?php echo $lang?></a></li>
<?php
endforeach;
?>
</ul>