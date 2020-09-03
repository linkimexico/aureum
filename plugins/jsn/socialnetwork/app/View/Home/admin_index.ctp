<?php
echo $this->Html->css(array('visualize'), null, array('inline' => false));
echo $this->Html->script(array('enhance', 'excanvas', 'visualize.jQuery'), array('inline' => false));
?>

<script>
function clearNotifications()
{
	jQuery.get('<?php echo $this->request->base?>/admin/admin_notifications/ajax_clear');
	jQuery("#notifications_list").slideUp();
}

jQuery(function(){
    jQuery('#reg_content .admin_chart').visualize({type: 'line', width: '480px'});
    
    jQuery('.chart-tabs li a').click(function(){
        jQuery('.chart-tabs li a').removeClass('current');
        jQuery(this).addClass('current');
        jQuery('.tab').hide();
        jQuery('#'+jQuery(this).attr('id')+'_content').show();
        
        jQuery('.visualize').remove();
        jQuery('#'+jQuery(this).attr('id')+'_content .admin_chart').visualize({type: 'line', width: '480px'});
    });
});
</script>

<?php echo $this->element('admin/adminnav', array("cmenu" => "dashboard"));?>

<div id="center">
	<div>
	    <div class="box2 box_style3">
    		<h3>Admin Notifications <a href="javascript:void(0)" style="float:right" class="tip" title="Clear all notifications" onclick="clearNotifications()"><i class="icon-check icon-large"></i></a></h3>
    		<div class="box_content">
        		<?php if ( empty($admin_notifications) ): ?>
        			<div style="margin-bottom:10px"><?php echo __('No new notifications')?></div>
        		<?php else: ?>
        		<ul class="list2" id="notifications_list" style="margin: -8px 0 10px 0;max-height: 190px;overflow: auto;">
        		<?php foreach ($admin_notifications as $noti): ?>
        			<li style="border-bottom:1px solid #DFDFDF;" <?php if (!$noti['AdminNotification']['read']) echo 'class="unread"';?>>
        				<a href="<?php echo $this->request->base?>/admin/admin_notifications/ajax_view/<?php echo $noti['AdminNotification']['id']?>" <?php if ( !empty($noti['AdminNotification']['message']) ):?>class="overlay" title="Notification Detail"<?php endif; ?>>
        					<b><?php echo h($noti['User']['name'])?></b> <?php echo $noti['AdminNotification']['text']?><br />
        					<span class="date"><?php echo $this->Jsnsocial->getTime( $noti['AdminNotification']['created'], $jsnsocial_setting['date_format'], $utz )?></span>
        				</a>
        			</li>
        		<?php endforeach; ?>
        		</ul>
        		<?php endif; ?>
    		</div>
    	</div>
	</div>

		
	
	<div style="line-height:1.5;">
		<h1>Admin Dashboard</h1>
		<div style="font-size: 12px;margin-bottom: 20px">
			<ul class="list8 admin_stats">
			    <li><i class="icon-user icon-large"></i><br />
			        <span><?php echo $user_count?></span><br />
			        Users
			    </li>
			    <li><i class="icon-edit icon-large"></i><br />
                    <span><?php echo $blog_count?></span><br />
                    Blogs
                </li>
			    <li><i class="icon-picture icon-large"></i><br />
                    <span><?php echo $photo_count?></span><br />
                    Photos
                </li>
                <li><i class="icon-facetime-video icon-large"></i><br />
                    <span><?php echo $video_count?></span><br />
                    Videos
                </li>
                <li><i class="icon-comment icon-large"></i><br />
                    <span><?php echo $topic_count?></span><br />
                    Topics
                </li>
                <li><i class="icon-group icon-large"></i><br />
                    <span><?php echo $group_count?></span><br />
                    Groups
                </li>
                <li><i class="icon-calendar icon-large"></i><br />
                    <span><?php echo $event_count?></span><br />
                    Events
                </li>
			</ul>
		</div>
		
		<ul class="list7 chart-tabs" id="feed-type" style="float:right;margin:-5px 0 5px 0">
            <li><a href="javascript:void(0)" class="current" id="reg">Registration</a></li>
            <li><a href="javascript:void(0)" id="act">Activities</a></li>
        </ul>
		<h2>Site Stats</h2>
		
		<div style="margin:30px 0 60px 10px">
		    <div id="reg_content" class="tab" style="display:block">
        		<table class="admin_chart" style='display:none'>
                    <caption>Registration Over Past 7 Days</caption>
                    <thead>
                        <tr>
                            <td></td>
                            <?php foreach ( $stats as $day => $stat ): ?>
                            <th scope="col"><?php echo $day?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">New Users</th>
                            <?php foreach ( $stats as $day => $stat ): ?>
                            <td><?php echo $stat['users']?></td>
                            <?php endforeach; ?> 
                        </tr>     
                    </tbody>
                </table>
            </div>
            
            <div id="act_content" class="tab">
                <table class="admin_chart" style='display:none'>
                    <caption>Activities Over Past 7 Days</caption>
                    <thead>
                        <tr>
                            <td></td>
                            <?php foreach ( $stats as $day => $stat ): ?>
                            <th scope="col"><?php echo $day?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>   
                        <tr>
                            <th scope="row">Activities</th>
                            <?php foreach ( $stats as $day => $stat ): ?>
                            <td><?php echo $stat['activities']?></td>
                            <?php endforeach; ?> 
                        </tr>    
                    </tbody>
                </table>
            </div>
        </div>
		
		<h2>Admin Notes</h2>
		<form action="<?php echo $this->request->base?>/admin/settings" method="post">
		<?php echo $this->Form->textarea('admin_notes', array('value' => $jsnsocial_setting['admin_notes'], 'style' => 'width:500px;height:200px')); ?>
		<div align="center" style="margin-top:10px"><input type="submit" class="button button-action button-medium" value="Save Notes"></div>
		</form>
	</div>
</div>