<script>
jQuery(document).ready(function(){
    jQuery('#everyone').click(function(){
        if ( jQuery('#everyone').is(':checked') )
        {
            jQuery('#permission_list li').hide();
            jQuery('#everyone').parent().show();
        }
        else
            jQuery('#permission_list li').show();
    });
});
</script>

<ul class="list6" id="permission_list">
	<li><label>Everyone</label>
        <?php echo $this->Form->checkbox('everyone', array('checked' => ( $permission === '' ) ) ); ?>
    </li>
    <?php 
    foreach ( $roles as $role ): 
    ?>
    <li style="<?php if ( $permission === '' ) echo 'display:none'; ?>"><label><?php echo $role['Role']['name']?></label>
        <input type="checkbox" name="permissions[]" value="<?php echo $role['Role']['id']?>" <?php if ( in_array($role['Role']['id'], explode(',', $permission))) echo 'checked';?>>
    </li>
    <?php 
    endforeach; 
    ?>
</ul>