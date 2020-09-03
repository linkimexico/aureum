<table width="100%" cellpadding="10" cellspacing="0" border="0">
	<tr>
		<td><?php echo __('%s invited you to join %s', $user['name'], $jsnsocial_setting['site_name'])?></td>
	</tr>
	<tr>
		<td style="padding:10px;background-color:#fff;border-left:none;border-right:none;border-top:1px solid #ccc;border-bottom:1px solid #ccc">
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td width="50" valign="top"><a href="<?php echo FULL_BASE_URL . $request->base?>/users/view/<?php echo $user['id']?>"><img width="50" src="<?php echo FULL_BASE_URL.'/'; ?><?php if ($user['avatar']) echo $user['avatar']; else echo 'components/com_jsn/assets/img/default.jpg';?>"></a></td>
					<td><a href="<?php echo FULL_BASE_URL . $request->base?>/users/view/<?php echo $user['id']?>"><b><?php echo $user['name']?></b></a><br />
						<?php echo __n( '%s friend', '%s friends', $user['friend_count'], $user['friend_count'] )?> . <?php echo __n( '%s photo', '%s photos', $user['photo_count'], $user['photo_count'] )?><br />
						<?php 
						if ( !empty($message) )
							echo '"' . nl2br(h($message)) . '"';
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo FULL_BASE_URL . $request->base?>"><?php echo __('Click here')?></a> <?php echo __('to visit')?> <?php echo $jsnsocial_setting['site_name']?></td>
	</tr>
</table>