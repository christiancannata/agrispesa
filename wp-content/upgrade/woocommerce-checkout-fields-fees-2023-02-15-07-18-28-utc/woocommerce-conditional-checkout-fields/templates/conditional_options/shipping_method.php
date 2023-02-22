<?php 
	$zones = WC_Shipping_Zones::get_zones();
	
	//wcccf_var_dump($zones);
	//wcccf_var_dump($zone_methods);
 ?>
<div class="wcccf_option_box_container">
	<label><?php esc_html_e('Method','woocommerce-conditional-checkout-fields');  ?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][shipping_method_id]" >
		<?php 
		
	if ( ! empty( $zones ) )  
		foreach ( $zones as $zone_id => $zone_data ) 
		{
			$zone = WC_Shipping_Zones::get_zone( $zone_id ); 
			$zone_methods = $zone->get_shipping_methods(); 
		
			if(isset($zone_methods))
			foreach($zone->get_shipping_methods() as $instance_id => $method)
			{	
					//1. Support to new Table Shipping Rating plugin rates (CodeCanyon)
					if(get_class($method) == 'BE_Table_Rate_Method')
					{
						$be_table_rates = get_option( $method->id . '_options-' . $method->instance_id );
						foreach($be_table_rates['settings'] as $be_rate)
						{
							$method_tile = $be_rate['title'];
							$shipping_rate_id = $instance_id."-".$be_rate['option_id'];
							$shipping_id = $method->id.":".$shipping_rate_id;
					?>
					<option value="<?php echo $shipping_id; ?>" <?php if(isset($coditional_item_data['shipping_method_id'])) selected( $coditional_item_data['shipping_method_id'], $shipping_id ); ?>><?php echo $zone->get_zone_name()." - ".esc_html( $method_tile ); ?></option>
					<?php
						}
					}
					//2. Support to Woo Table Shipping Rating plugin
					elseif(method_exists($method, 'get_shipping_rates'))
					{
						$shipping_rates = $method->get_shipping_rates();
						foreach($shipping_rates as $shipping_rate)
						{
								
								$method_tile = $zone_methods[$shipping_rate->shipping_method_id]->title; //$shipping_rate->rate_label;
								$method_sub_title = $shipping_rate->rate_label;
								$shipping_rate_id = $instance_id.":".$shipping_rate->rate_id;
								$shipping_id = $method->id.":".$shipping_rate_id;
						?>
						<option value="<?php echo $shipping_id; ?>" <?php if(isset($coditional_item_data['shipping_method_id'])) selected( $coditional_item_data['shipping_method_id'], $shipping_id ); ?>><?php echo $zone->get_zone_name()." - ".esc_html( $method_tile ); ?></option>
						<?php
						}
					}
					//3. Native WooCommerce methods
					else
					{
						$method_tile = $method->get_title();
						$shipping_id =  $method->id.":".$instance_id;
					?>
						<option value="<?php echo $shipping_id; ?>" <?php if(isset($coditional_item_data['shipping_method_id'])) selected( $coditional_item_data['shipping_method_id'], $shipping_id ); ?>><?php echo $zone->get_zone_name()." - ".esc_html( $method_tile ); ?></option>
					<?php
					}
			}
	}?>
	</select>
</div>
<div class="wcccf_option_box_container"> 
	<label><?php esc_html_e('Operator','woocommerce-conditional-checkout-fields');?></label>
	<select name="wcccf_field_data[<?php echo $field_id; ?>][conditional_group_item][<?php echo $conditional_item_id; ?>][shipping_method_operator]" >
		<option value="equal" <?php if(isset($coditional_item_data['shipping_method_operator'])) selected( $coditional_item_data['shipping_method_operator'], 'equal' ); ?>><?php esc_html_e('Equal','woocommerce-conditional-checkout-fields');?></option>
		<option value="not_equal" <?php if(isset($coditional_item_data['shipping_method_operator'])) selected( $coditional_item_data['shipping_method_operator'], 'not_equal' ); ?>><?php esc_html_e('Not equal','woocommerce-conditional-checkout-fields');?></option>
	</select>
</div> 