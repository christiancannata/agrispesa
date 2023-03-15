<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class Smart_Manager_Pricing {

	public static function sm_show_pricing() {

		$utm_medium = apply_filters( 'sm_pricing_page_utm_medium', 'in_app_pricing' );

		?>
		<style type="text/css">
			.update-nag {
				display: none;
			}
			.wrap.about-wrap.sm {
				margin: 0 auto;
				max-width: 100%;
			}
			body{
				background-color: white;
			}
			.sm_main_heading {
				font-size: 2em;
				background-color: #252f3f !important;
				color: #ffffff;
				text-align: center;
				font-weight: 500;
				margin: auto;
				padding-top: 0.75em;
				padding-bottom: 0.5em;
				/* max-width: 1375px; */
			}
			.sm_discount_code {
				/* color: #6875F5; */
				font-weight: 600;
				font-size: 2.5rem;
			}
			.sm_sub_headline {
				font-size: 1.6em;
				font-weight: 400;
				color: #00848D !important;
				text-align: center;
				line-height: 1.5em;
				margin: 0 auto 1em;
			}
			.sm_row {
				/* padding: 1em !important;
				margin: 1.5em !important; */
				clear: both;
				position: relative;
			}
			#sm_price_column_container {
				display: -webkit-box;
				display: -webkit-flex;
				display: -ms-flexbox;
				display: flex;
				max-width: 1190px;
				margin-right: auto;
				margin-left: auto;
				margin-top: 4em;
				padding-bottom: 4em;
			}
			.sm_column {
				padding: 2em;
				margin: 0 1em;
				background-color: #fff;
				border: 1px solid rgba(0, 0, 0, 0.1);
				text-align: center;
				color: rgba(0, 0, 0, 0.75);
			}
			.column_one_fourth {
				width: 18%;
				border-radius: 3px;
				margin-right: 4%;
			}
			.sm_last {
				margin-right: 0;
			}
			.sm_price {
				margin: 1.5em 0;
				color: #1e73be;
			}
			.sm_button {
				color: #FFFFFF !important;
				padding: 15px 32px;
				text-align: center;
				text-decoration: none;
				display: inline-block;
				font-size: 16px;
				font-weight: 500;
				margin: 2em 2px 1em 2px;
				cursor: pointer;
			}
			.sm_button.green {
				background: #23B191;
				border-color: #23B191;
			}
			.sm_button.green:hover {
				background: #66C78E;
				border-color: #66C78E;
			}
			.sm_button.small {
				text-transform: uppercase !important;
				box-shadow: none;
				padding: 0.8em;
				font-size: 1rem;
				border-radius: 0.25rem;
				margin-top: 1em;
				font-weight: 600;
			}
			.sm_discount_amount {
				font-size: 1.3em !important;
			}
			.dashicons.dashicons-yes {
				color: green;
				font-size: 2em;
			}
			.dashicons.dashicons-no-alt {
				color: #ed4337;
				font-size: 2em;
			}
			.dashicons.dashicons-yes.yellow {
				color: #BDB76B;
				line-height: unset;
			}
			.dashicons.dashicons-awards,
			.dashicons.dashicons-testimonial {
				margin-right: 0.25em !important;
				color: #15576F;
				font-size: 1.25em;
			}
			.sm_license_name {
				font-size: 1.1em !important;
				color: #1a72bf !important;
				font-weight: 500 !important;
			}
			.sm_old_price {
				font-size: 1.3em;
				color: #ed4337;
				vertical-align: top;
			}
			.sm_new_price {
				font-size: 1.6em;
				padding-left: 0.2em;
				font-weight: 400;
			}
			.sm_most_popular {
				position: absolute;
				right: 0px;
				top: -39px;
				background-color: #41495b;
				background-color: #596174;
				text-align: center;
				color: white;
				padding: 10px;
				font-size: 18px;
				border-top-right-radius: 4px;
				border-top-left-radius: 4px;
				font-weight: 500;
				width: 275px;
			}
			#sm-testimonial {
				text-align: center;
			}
			.sm-testimonial-content {
				width: 50%;
				margin: 0 auto;
				margin-bottom: 1em;
				background-color: #FCFEE9;
			}
			.sm-testimonial-content img {
				width: 12% !important;
				border-radius: 9999px;
			}
			.sm_testimonial_headline {
				margin: 0.6em 0 !important;
				font-weight: 500 !important;
				font-size: 1.5em !important;
			}
			.sm_testimonial_text {
				text-align: left;
				font-size: 1.2em;
				line-height: 1.6;
				padding: 1em;
			}
			.pricing {
				border-radius: 5px;
				position: relative;
				padding: 0.25em;
				margin: 2em auto;
				background-color: #fff;
				border: 1px solid rgba(0, 0, 0, 0.1);
				text-align: center;
				color: rgba(0, 0, 0, 0.75);
			}
			.pricing h4 {
				margin-bottom: 1em;
			}
			.pricing del {
				font-size: 1.3em;
				color: grey;
			}
			.pricing h2 {
				margin-top: 0!important;
				margin-bottom: 0.5em;
				text-align: center;
				font-weight: 600;
				line-height: 1.218;
				color: #515151;
				font-size: 2.5em;
			}
			.pricing p {
				text-align: center;
				margin: 0em;
			}
			.pricing:hover{
				border-color: #15576F;
			}
			.pricing.scaleup{
				transform: scale(1.2);
			}
			.fidget.spin{
				animation: spin 1.2s 0s linear both infinite;
			}
			@keyframes spin {
				0% {
						transform: rotate(0deg); 
					}
				100% {
						transform: rotate(360deg); 
					} 
			}
			table.sm_table {
				width: 70%;
				margin-left: 15%;
				margin-right: 15%;
			}
			table.sm_table th,
			table.sm_table tr,
			table.sm_table td,
			table.sm_table td span {
				padding: 0.5em;
				text-align: center !important;
				background-color: transparent !important;
				vertical-align: middle !important;
			}
			table.sm_table,
			table.sm_table th,
			table.sm_table tr,
			table.sm_table td {
				border: 1px solid #eaeaea;
			}
			table.sm_table.widefat th,
			table.sm_table.widefat td {
				color: #515151;
			}
			table.sm_table th {
				font-weight: bolder !important;
				font-size: 1.1em;
			}
			table.sm_table tr td {
				font-size: 15px;
			}
			table.sm_table th.sm_table_headers_gray {
				background-color: #F4F4F4 !important;
				color: #A1A1A1 !important;
			}
			table.sm_table th.sm_free_features {
				background-color: #F7E9C8 !important;
				color: #D39E22 !important;
			}
			table.sm_table th.sm_pro_features {
				background-color: #DCDDFC !important;
				color: #6875F5 !important;
			}
			table.sm_table td{
				padding: 0.5em;
			}
			table.sm_table td.sm_feature_name {
				text-transform: capitalize;
			}
			table.sm_table td.sm_free_feature_name {
				background-color: #FCF7EC !important;
			}
			table.sm_table td.sm_pro_feature_name {
				background-color: #F4F5FD !important;
			}
			#sm_product_page_link {
				text-align: center;
				font-size: 1.2em;
				margin-top: 2em;
				line-height: 2em;
			}
			.clr-a {
				color: #00848D !important;
			}
			.update-nag , .error, .updated{ 
				display:none; 
			}
			table .dashicons {
				padding-top: 0 !important;
			}
			#wpcontent {
				padding-left: 0!important;
			}
			#sm-testimonial-others, #sm_comparison_table, #sm_activity{
				margin-top: 4em;
			}

		</style>

		<div class="wrap about-wrap sm">
			<div class="sm_row" id="sm-pricing">
				<?php if( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ){ ?>
					<style type="text/css">
						.sa_offer {
							margin: 1em auto;
							text-align: center;
							font-size: 1.2em;
							line-height: 1em;
							padding: 1em;
						}
						.sa_offer_content img {
							width: 55%;
						}
					</style>
					<div class="sa_offer">
						<div class="sa_offer_content">
							<a href="https://www.storeapps.org/woocommerce-plugins/?utm_source=in_app_pricing&utm_medium=sm_banner&utm_campaign=sa_bfcm_2022" target="_blank">
								<img src="<?php echo esc_url( plugins_url( 'sa-includes/images/bfcm-2022.png', (dirname( SM_PLUGIN_FILE )) . '/classes/sa-includes/' ) ); ?>" />
							</a>
						</div>
					</div>
				<?php } else { ?>
					<div class="sm_main_heading">
						<div style="display: inline-flex;">
							<div style="padding-right: 0.5rem;">🎉</div>
							<div style="line-height: 1.5rem;"><?php echo sprintf( __( 'Congratulations! You just unlocked %s on Smart Manager Pro', 'smart-manager-for-wp-e-commerce' ), '<span class="sm_discount_code">' . __( '25% off', 'smart-manager-for-wp-e-commerce' ) . '</span>' ); ?></div>
							<div style="padding-left: 0.5rem;">🎉</div>
						</div>
						<div style="padding-top: 1em;font-size: 0.5em;"><?php echo __( '⏰ Limited time offer', 'smart-manager-for-wp-e-commerce' ); ?></div>
					</div>
				<?php } ?>
				<div id="sm_price_column_container">
					<div class="sm_column column_one_fourth pricing">
						<span class="sm_plan"><h4 class="clr-a center"><?php echo __( '1 site (Annual)', 'smart-manager-for-wp-e-commerce' ); ?></h4></span>
						<span class="sm_price">
							<p><del class="center"><?php echo __( '$199', 'smart-manager-for-wp-e-commerce' ); ?></del></p>
							<h2><?php echo ( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ) ? __( '$99', 'smart-manager-for-wp-e-commerce' ) : __( '$149', 'smart-manager-for-wp-e-commerce' ); ?></h2>
						</span>

						<div class="center">
							<a class="sm_button small green center" href="https://www.storeapps.org/?buy-now=18694&qty=1<?php echo ( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ) ? '' : '&coupon=sm-25off'; ?>&page=722&with-cart=1&utm_source=sm&utm_medium=<?php echo $utm_medium; ?>&utm_campaign=single_annual" target="_blank" rel="noopener"><?php _e( 'Buy Now', 'smart-manager-for-wp-e-commerce' ); ?></a>
						</div>
					</div>
					<div class="sm_column column_one_fourth pricing scaleup" style="border-color: #15576F;padding: 0;border-width: 0.2em;">
						<div style="text-align: center;background-color: #15576F;color: #FFF;padding: 1em;font-weight: 900;text-transform: uppercase;"> <?php echo __( 'Best Seller', 'smart-manager-for-wp-e-commerce' ); ?> </div>
						<span class="sm_plan"><h4 class="clr-a center"><?php echo __( '5 sites (Annual)', 'smart-manager-for-wp-e-commerce' ); ?></h4></span>
						<span class="sm_price">
							<p><del class="center"><?php echo __( '$249', 'smart-manager-for-wp-e-commerce' ); ?></del></p>
							<h2><?php echo ( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ) ? __( '$124', 'smart-manager-for-wp-e-commerce' ) : __( '$187', 'smart-manager-for-wp-e-commerce' ); ?></h2>
						</span>

						<div class="center">
							<a class="sm_button small green center" href="https://www.storeapps.org/?buy-now=18693&qty=1<?php echo ( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ) ? '' : '&coupon=sm-25off'; ?>&page=722&with-cart=1&utm_source=sm&utm_medium=<?php echo $utm_medium; ?>&utm_campaign=multi_annual" target="_blank" rel="noopener"><?php _e( 'Buy Now', 'smart-manager-for-wp-e-commerce' ); ?><span style="width: 1em; height: 1em; background-image: url('https://www.storeapps.org/wp-content/themes/storeapps/assets/images/fidget.svg'); display: inline-block; margin-left: 0.5em" class="fidget spin"></span></a>
						</div>
					</div>
					<div class="sm_column column_one_fourth pricing sm_lifetime_price">
						<span class="sm_plan"><h4 class="clr-a center"><?php echo __( '1 site (Lifetime)', 'smart-manager-for-wp-e-commerce' ); ?></h4></span>
						<span class="sm_price">
							<p><del class="center"><?php echo __( '$549', 'smart-manager-for-wp-e-commerce' ); ?></del></p>
							<h2><?php echo ( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ) ? __( '$274', 'smart-manager-for-wp-e-commerce' ) : __( '$412', 'smart-manager-for-wp-e-commerce' ); ?></h2>
						</span>

						<div class="center">
							<a class="sm_button small green center" href="https://www.storeapps.org/?buy-now=86835&qty=1&coupon=sm-25off-l&page=722&with-cart=1&utm_source=sm&utm_medium=<?php echo $utm_medium; ?>&utm_campaign=single_lifetime" target="_blank" rel="noopener"><?php _e( 'Buy Now', 'smart-manager-for-wp-e-commerce' ); ?></a>
						</div>
					</div>
					<div class="sm_column column_one_fourth pricing sm_last sm_lifetime_price">
						<span class="sm_plan"><h4 class="clr-a center"><?php echo __( '5 sites (Lifetime)', 'smart-manager-for-wp-e-commerce' ); ?></h4></span>
						<span class="sm_price">
							<p><del class="center"><?php echo __( '$599', 'smart-manager-for-wp-e-commerce' ); ?></del></p>
							<h2><?php echo ( defined('SA_OFFER_VISIBLE') && true === SA_OFFER_VISIBLE ) ? __( '$299', 'smart-manager-for-wp-e-commerce' ) : __( '$449', 'smart-manager-for-wp-e-commerce' ); ?></h2>
						</span>

						<div class="center">
							<a class="sm_button small green center" href="https://www.storeapps.org/?buy-now=86836&qty=1&coupon=sm-25off-l&page=722&with-cart=1&utm_source=sm&utm_medium=<?php echo $utm_medium; ?>&utm_campaign=multi_lifetime" target="_blank" rel="noopener"><?php _e( 'Buy Now', 'smart-manager-for-wp-e-commerce' ); ?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="sm_row" id="sm-testimonial">
				<div class="sm_column sm-testimonial-content">
					<?php
					echo apply_filters( 'sm_pricing_page_testimonial_1', '<img src='. SM_IMG_URL .'jeff-smith.png alt="Jeff" />
						<h3 class="sm_testimonial_headline">'. __( 'I would happily pay five times for this product!', 'smart-manager-for-wp-e-commerce' ) .'</h3>
						<div class="sm_testimonial_text">
							'. __( 'What really sold me on Smart Manager Pro was Bulk Edit. My assistant does not have to do any complex math now (earlier, I always feared she would make mistakes)! With Smart Manager, she has more free time at hand, so I asked her to set up auto responder emails. The response was phenomenal. Repeat sales were up by 19.5%.', 'smart-manager-for-wp-e-commerce' ) .'<br>
							- '. __( 'Jeff', 'smart-manager-for-wp-e-commerce' ) .'
						</div>' );
					?>
				</div>
			</div>
			<div class="sm_row" id="sm_comparison_table">
				<div class="sm_sub_headline"><span class="dashicons dashicons-awards"></span><?php echo __( ' More powerful features with Smart Manager Pro!', 'smart-manager-for-wp-e-commerce' ); ?></div>
				<table class="sm_table wp-list-table widefat fixed">
					<thead>
						<tr>
							<th class="sm_table_headers_gray">
								<?php echo esc_html__( 'Features', 'smart-manager-for-wp-e-commerce' ); ?>
							</th>
							<th class="sm_free_features">
								<?php echo esc_html__( 'Free', 'smart-manager-for-wp-e-commerce' ); ?>
							</th>
							<th class="sm_pro_features">
								<?php echo esc_html__( 'Pro', 'smart-manager-for-wp-e-commerce' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Supported Post Types', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes yellow'></span><br>
								<?php echo __( 'WordPress Posts, WooCommerce Products, Product Variations, Orders, Coupons', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span><br>
								<?php echo __( 'Everything in Lite +', 'smart-manager-for-wp-e-commerce' ); ?><br>
								<?php echo __( 'WordPress Pages, Media, Users, SEO plugins, WooCommerce Subscriptions, Bookings, Memberships, Product Add-ons, Brands…all', 'smart-manager-for-wp-e-commerce' ); ?>
								<strong><?php echo __( 'custom post types and their custom fields.', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Inline editing', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes yellow'></span><br>
								<?php echo __( 'Upto three records without saving', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span><br>
								<?php echo __( 'Unlimited', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Add or Delete records', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Columns (Show / Hide / Sort)', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Simple Search', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Advanced Search using “AND” filter', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Advanced Search using “OR” filter', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<strong><?php echo __( 'Bulk Edit / Batch Update', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span><br>
								<?php echo __( 'Set to, Append, Prepend, Increase / Decrease by %, Increase / Decrease by number, Set datetime to, Set date to, Set time to, Upload images and many more...', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Export all / Filtered records as CSV', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Duplicate single / multiple records', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Duplicate all records in a single click', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<strong><?php echo __( 'Create Column Sets / Custom Views', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Print PDF invoices', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Manage WordPress User roles', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Print packing slips for WooCommerce orders in bulk', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'View Customer Lifetime Value (LTV)', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<strong><?php echo __( 'Manage Custom Taxonomies', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Import', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<?php echo __( 'Coming soon', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Support', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								<span class='dashicons dashicons-yes'></span><br>
								<?php echo __( 'WP forum', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span><br>
								<?php echo __( 'Email / Call', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td class="sm_feature_name">
								<?php echo __( 'Pricing', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td class="sm_free_feature_name">
								-
							</td>
							<td class="sm_pro_feature_name">
								<div class="center">
									<a class="sm_button small green center" href="#sm_price_column_container" style="text-transform: none;"><?php _e( 'Buy Smart Manager Pro', 'smart-manager-for-wp-e-commerce' ); ?></a>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="sm_row" id="sm-testimonial-others">
				<div style="width: 70%; margin: 0 auto; display: flex; gap: 2em;">
					<div class="sm_column sm-testimonial-content">
						<?php
							echo apply_filters( 'sm_pricing_page_testimonial_2', '<img src='. SM_IMG_URL .'acrom.png alt="Acrom" />
							<h3 class="sm_testimonial_headline">'. __( 'A whole afternoon’s work in seconds', 'smart-manager-for-wp-e-commerce' ) .'</h3>
							<div class="sm_testimonial_text">
								'. __( 'We recommend Smart Manager Pro to everyone who is looking for an extremely multi-functional store manager. The plugin is absolutely worth it. Never again without Smart Manager!', 'smart-manager-for-wp-e-commerce' ) .'<br>
								- '. __( 'Acrom', 'smart-manager-for-wp-e-commerce' ) .'
							</div>' );
						?>
					</div>
					<div class="sm_column sm-testimonial-content">
						<?php 
							echo apply_filters( 'sm_pricing_page_testimonial_3', '<img src='. SM_IMG_URL .'bryan-batcher.jpeg alt="Bryan Batcher" />
							<h3 class="sm_testimonial_headline">'. __( '20 or 30 times quicker store management', 'smart-manager-for-wp-e-commerce' ) .'</h3>
							<div class="sm_testimonial_text">
								'. __( 'I\'ve got over 200 products and dreaded managing them one by one. Smart Manager Pro plugin lets me do this so much quicker. Probably 20 or 30 times quicker. It is an absolutely invaluable tool.', 'smart-manager-for-wp-e-commerce' ) .'<br>
								- '. __( 'Brian Batcher', 'smart-manager-for-wp-e-commerce' ) .'
							</div>' );
						?>
					</div>
				</div>
			</div>
			<div class="sm_row" id="sm_activity" style="width: 70%; margin: 0 auto; margin-top: 4em;">
				<div class="sm_sub_headline"> <?php echo esc_html__( 'Still hesitant to buy?', 'smart-manager-for-wp-e-commerce' ); ?> </div>
				<p> <?php echo esc_html__( 'Opening a product, editing it for the price, stock, description involves a significant amount of time. The same goes for orders, coupons, blog posts, users, any WordPress post type. For thousands of such records, the time spent, frustration, stress, calculation errors keep piling up.', 'smart-manager-for-wp-e-commerce' ); ?> </p>
				<table class="sm_table wp-list-table widefat fixed" style="width:100% !important; margin:0 !important;">
					<thead>
						<tr>
							<th class="sm_table_headers_gray">
								<?php echo esc_html__( 'Current Activity', 'smart-manager-for-wp-e-commerce' ); ?>
							</th>
							<th class="sm_table_headers_gray">
								<?php echo esc_html__( 'Average time spent without Smart Manager', 'smart-manager-for-wp-e-commerce' ); ?>
							</th>
							<th class="sm_table_headers_gray">
								<?php echo esc_html__( 'Average time spent using Smart Manager', 'smart-manager-for-wp-e-commerce' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo __( 'Add a new product / order / coupon or any post type record / OR open a record to edit price, stock, other fields', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td >
								<?php echo __( '3 mins', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td>
								<?php echo __( '30 seconds (Inline edit)', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo __( 'Open 100s and 1000s of records to make edits for the price, status, stock, etc.', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td >
								<?php echo __( '3 hours - 30 hours', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td>
								<?php echo __( 'Around 2 mins (Bulk edit)', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo __( 'Search for any record and make edits', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td >
								<?php echo __( '3 mins', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td>
								<?php echo __( '1 min (Advanced search and Inline edit)', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo __( 'Create duplicates for 100s and 1000s of records', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td >
								<?php echo __( '3 hours - 30 hours', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
							<td>
								<?php echo __( 'Around 2 mins (Duplicate)', 'smart-manager-for-wp-e-commerce' ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?php echo __( 'Total', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
							<td >
								<strong><?php echo __( '> 6 hours - 60 hours', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
							<td>
								<strong><?php echo __( 'Around 6 mins', 'smart-manager-for-wp-e-commerce' ); ?></strong>
							</td>
						</tr>
					</tbody>
				</table>
				<p> <?php echo esc_html__( 'This is just one situation. Imagine the time you’ll spend doing the same for thousands of records every month for other post types as well.', 'smart-manager-for-wp-e-commerce' ); ?> </p>
				<p> <?php echo esc_html__( 'So if you want to save your valuable time, it’s high time you switch to Smart Manager. Massive time-savings and 10x productivity boost.' ); ?> </p>
				<div style="text-align: center;"><a class="sm_button small green center" href="#sm_price_column_container" style="text-transform: none;"><?php _e( 'Select a plan now', 'smart-manager-for-wp-e-commerce' ); ?></a></div>
			</div>
		</div>
		<?php
	}
}

new Smart_Manager_Pricing();
