<?php
if ( !empty( $custom_fields ) )
{
	foreach ( $custom_fields as $field )
	{
		if ( $field['ProfileField']['type'] == 'heading' && !empty( $show_heading ) )
        {
            echo '<li class="fields_heading"><h2>' . $field['ProfileField']['name'] . '</h2></li>';
            continue;
        }
            
		$val = ( isset( $values[$field['ProfileField']['id']]['value'] ) ) ? $values[$field['ProfileField']['id']]['value'] : '';
		
		echo '<li><label>' .$field['ProfileField']['name'];
		
		if ( !empty( $field['ProfileField']['description'] ) )
			echo ' <a href="javascript:void(0)" class="tip" title="' . $field['ProfileField']['description'] . '">(?)</a>';
		echo '</label>';
		
		switch ( $field['ProfileField']['type'] )
		{                
			case 'textfield':
				echo $this->Form->text( 'field_' .$field['ProfileField']['id'], array( 'value' => $val ) );
				break;
			
			case 'textarea':
				echo $this->Form->textarea( 'field_' .$field['ProfileField']['id'], array( 'value' => $val ) );
				break;
				
			case 'list':
				$options = array();
				$field_values = explode( "\n", $field['ProfileField']['values'] );
				
				foreach ( $field_values as $value )
					$options[trim($value)] = trim($value);

				/*if ( !empty( $multiple) )
					echo $this->Form->select( 'field_' .$field['ProfileField']['id'], $options, array( 'value' => $val, 'multiple' => 'multiple', 'class' => 'multi') );
				else*/
					echo $this->Form->select( 'field_' .$field['ProfileField']['id'], $options, array( 'value' => $val ) );
				break;
				
			case 'multilist':
				$options = array();
				$field_values = explode( "\n", $field['ProfileField']['values'] );
				
				foreach ( $field_values as $value )
					$options[trim($value)] = trim($value);
					
				echo $this->Form->select( 'field_' .$field['ProfileField']['id'], $options, array( 'value' => explode(', ', $val), 'multiple' => 'multiple', 'class' => 'multi' ) );
				break;
		}
		
		if ( !empty( $show_require ) && $field['ProfileField']['required'] )
			echo ' *';
		
		echo '</li>';
	}
}