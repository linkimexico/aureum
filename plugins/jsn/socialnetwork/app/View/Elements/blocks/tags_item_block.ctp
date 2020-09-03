<?php
if (!empty($tags))
{
    echo '<ul class="tags">';
	foreach ($tags as $tag)
		echo '<li><a href="' . $this->request->base . '/tags/view/'.h($tag).'">'.h($tag).'</a></li>';	
    echo '</ul>';
}
else
	echo __('Nothing found');
?>