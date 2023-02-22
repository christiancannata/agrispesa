<h4><?php esc_html_e('Conditional logic','woocommerce-conditional-checkout-fields');?> <a class="page-title-action wcccuf_expand_conditional_options"><?php esc_html_e('Collapse / Expand','woocommerce-conditional-checkout-fields');?> </a></h4>
<div class="wcccuf_conditional_box_expandible"> 
	<div id="conditional_options_box_content_<?php echo $field_id; ?>" class="conditional_options_box_content">
		<?php 	
				if(!empty($conditional_options))
					$this->render_conditional_group_item($field_id, 0, $conditional_options); 
		?>
	</div>
	<div class="wcccf_loader" id="group_conditional_options_loader_<?php echo $field_id; ?>"></div>
	<button class="button button-primary add_new_conditional_group_item" data-id="<?php echo $field_id; ?>"><?php esc_html_e('Add new group (Or relationship)','woocommerce-conditional-checkout-fields');?></button>
</div>