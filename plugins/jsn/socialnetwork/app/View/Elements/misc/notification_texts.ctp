<?php
if ( empty( $noti['Notification']['action'] ) ) // backward compability (prior to 1.1)
	echo $noti['Notification']['text'];				

switch ( $noti['Notification']['action'] )
{
	case 'profile_comment':
		echo __('wrote on your profile');
		break;
		
	case 'status_comment':
		$params = unserialize( $noti['Notification']['params'] );
		echo __('commented on %s status', possession( $params['actor'], $params['owner'] ));
		break;
		
	case 'own_status_comment':
		echo __('commented on your status');
		break;
		
	case 'message_reply':
		echo __('replied to "%s"', h($noti['Notification']['params']) );
		break;
		
	case 'photo_comment':
		$params = unserialize( $noti['Notification']['params'] );
		echo __( 'commented on %s photo', possession( $params['actor'], $params['owner'] ) );
		break;
		
	case 'own_photo_comment':
		echo __('commented on your photo');
		break;
		
	case 'item_comment':
		echo __( 'commented on "%s"', $noti['Notification']['params'] );
		break;
		
	case 'photo_like':
		echo __( 'likes your photo' );
		break;
		
	case 'item_like':
		echo __( 'likes "%s"', h($noti['Notification']['params']) );
		break;
		
	case 'activity_like':
		echo __( 'likes your status' );
		break;
		
	case 'message_send':
		echo __('sent you a message');
		break;
		
	case 'conversation_add':
		echo __('added you to a conversation');
		break;
		
	case 'event_invite':
		echo __( 'invited you to "%s"', h($noti['Notification']['params']) );
		break;
		
	case 'friend_add':
		echo __('wants to be friends with you');
		break;
		
	case 'friend_accept':
		echo __('accepted your friend request');
		break;
		
	case 'group_request':
		echo __( 'requested to join "%s"', h($noti['Notification']['params']) );
		break;
		
	case 'group_invite':
		echo __( 'invited you to join "%s"', h($noti['Notification']['params']) );
		break;
		
	case 'photo_tag':
		echo __('tagged you in a photo');
		break;
}