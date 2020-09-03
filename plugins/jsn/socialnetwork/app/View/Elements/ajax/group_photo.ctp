<?php if ( !empty( $is_member ) ): ?> 
<a href="javascript:void(0)" onclick="loadPage('photos', '<?php echo $this->request->base?>/photos/ajax_upload/group/<?php echo $target_id?>')" class="topButton button button-action" style="margin-top: -40px"><?php echo __('Upload Photos')?></a>
<?php endif; ?>

<?php echo $this->element( 'lists/photos_list', array( 'type' => APP_GROUP ) ); ?>