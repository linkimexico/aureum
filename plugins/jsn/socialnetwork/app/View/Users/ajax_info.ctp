<div class="post_body">
	<div id="jsnsocial_profilefields">
	<?php
	JRequest::setVar('view','profile');
	JRequest::setVar('id',$user['User']['id']);
	JsnHelper::getUserProfile($user['User']['id'],'default_fields');
	if ( $uid == $user['User']['id'] ) JsnHelper::getUserProfile($user['User']['id'],'default_params');
	?>
	</div>
	
	<script>
		<?php
		$doc=JFactory::getDocument();
		if(isset($doc->_script['text/javascript'])) echo($doc->_script['text/javascript']);
		
		?>
	</script>
    <h2><?php echo __('Recent Likes')?></h2>
    <?php 
    if ( !empty( $items ) )
    {
    	foreach ($items as $type => $items)
    	{ 
    		echo '<div style="margin-bottom:5px">';
    		
    		switch ( $type )
    		{
    			case 'blog':
    				echo __('Blogs');					
    				break;
    				
    			case 'topic':
    				echo __('Topics');
    				break;
    				
    			case 'album':
    				echo __('Albums');
    				break;
    				
    			case 'video':
    				echo __('Videos');
    				break;
    		}
    		
    		echo ': ';
    		$model = ucfirst( $type );
    		
    		foreach ( $items as $key => $item )
    		{
    			echo '<a href="' . $this->request->base . '/' . $type . 's/view/' . $item[$model]['id'] . '" class="tip" title="' . __('by %s', h($item['User']['name'])) . ', ' . __n( '%s like', '%s likes', $item[$model]['like_count'], $item[$model]['like_count'] ) . '">' . h($item[$model]['title']) . '</a>';						
    			if ( $key != ( count( $items ) - 1 ) ) 
    				echo ', ';
    		}
    		
    		echo '</div>';	 
    	}	
    } 
    else
    	echo '<div align="center">' . __('Nothing found') . '</div>';
    ?>
</div>