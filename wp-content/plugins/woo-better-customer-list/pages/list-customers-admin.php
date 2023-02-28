<?php
/**
* Main List Customers page
*
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wpdb;

function request_URI() {

	if(!isset($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		if($_SERVER['QUERY_STRING']) {
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
	return $_SERVER['REQUEST_URI'];
}

$_SERVER['REQUEST_URI'] = request_URI();

?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e('Customers', 'woo-better-customer-list' ); ?></h1>
	<?php

	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = isset( $_GET['rows'] ) ? absint( $_GET['rows'] ) : 20;
	$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
	$metakey = isset( $_GET['metakey'] ) ? $_GET['metakey'] : '';
	$order = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
	$offset = ( $pagenum - 1 ) * $limit;
	$customerstatus = isset( $_GET['cusstatus'] ) ? $_GET['cusstatus'] : '';
	$customeraverage = isset( $_GET['cusaverage'] ) ? $_GET['cusaverage'] : '';
	$customerordered = isset( $_GET['cusordered'] ) ? $_GET['cusordered'] : '';
    $search = '';

	if (isset($_GET['s']) && !empty($_GET['s'])) {
		$search = '*'.$_GET['s'].'*';

		$args = array(
			'role'         => 'customer',
			'number'       => $limit,
			'orderby'      => $orderby,
			'meta_key'     => $metakey,
			'order'				 => $order,
			'offset'       => $offset,
			'search'       => $search
		);
	} else {
		$args = array(
			'role'         => 'customer',
			'number'       => $limit,
			'orderby'      => $orderby,
			'meta_key'     => $metakey,
			'order'				 => $order,
			'offset'       => $offset
		);
	}

	if (isset($customerstatus) && !empty($customerstatus)) {
		$argscs = array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => 'customer_status',
					'value'   => $customerstatus,
					'compare' => '='
				)
			)
		);
		$args = array_merge($args,$argscs);
	}

	if (isset($customeraverage) && !empty($customeraverage)) {
		if ($customeraverage == 'set') {
			$argsca = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'customer_average',
						'value'   => 0,
						'compare' => '>'
					)
				)
			);
		} elseif ($customeraverage == 'notset') {
			$argsca = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'customer_average',
						'value'   => 0,
						'compare' => '='
					)
				)
			);
		}

		$args = array_merge($args,$argsca);
	}

	if (isset($customerordered) && !empty($customerordered)) {
		if ($customerordered == 'ordered') {
			$argsco = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '_order_count',
						'value'   => 0,
						'compare' => '>'
					)
				)
			);
		} elseif ($customerordered == 'notordered') {
			$argsco = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '_order_count',
						'value'   => 0,
						'compare' => '='
					)
				)
			);
		}

		$args = array_merge($args,$argsco);
	}

	$listusersquery = new WP_User_Query( $args );

	$listusers = $listusersquery->get_results();

	if(isset($listusers) && !empty($listusers)) {

		if ($customerstatus || $customeraverage || $customerordered || $search) {
			$cusnum = count_users();
			$displaynum = $cusnum['avail_roles']['customer'];
			$number_of_users = $listusersquery->get_total();
		} else {
			$cusnum = count_users();
			$displaynum = $cusnum['avail_roles']['customer'];
			$number_of_users = $cusnum['avail_roles']['customer'];
		}

		$num_of_pages = ceil( $number_of_users / $limit );

		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'total' => $num_of_pages,
			'current' => $pagenum
		) );
	} else {
		$cusnum = '';
		$number_of_users = '';
		$displaynum = 0;
		$num_of_pages = '';
		$page_links = '';
	}
	?>
	<form method="get" style="width: 75%;float: right;margin-top: 10px;">
		<input type="hidden" name="page" value="blz-bcl-list-customers" />
		<p class="search-box">
			<label class="screen-reader-text" for="user-search-input"><?php _e('Search Customers', 'woo-better-customer-list' ); ?>:</label>
			<input type="search" id="user-search-input" name="s" value="<?php $searchval = ($search) ? $search : ''; echo $searchval; ?>">
			<input type="submit" id="search-submit" class="button" value="<?php _e('Search Users', 'woo-better-customer-list' ); ?>">
		</p>
		<?php if ($customerstatus || $customeraverage || $customerordered || $search): ?>
			<div style="float: right; margin: 5px 15px;"><?php echo $number_of_users; ?> <?php _e('results', 'woo-better-customer-list' ); ?></div>
		<?php endif; ?>
	</form>

	<div class="tablenav" style="margin-bottom: 10px;">
		<span style="margin-right: 10px;"><?php echo $displaynum; ?> <?php _e('Customers', 'woo-better-customer-list' ); ?></span>
		<form method="get" style="display: inline-block;">
			<input type="hidden" name="page" value="blz-bcl-list-customers" />
			<label for="cus-status" class="screen-reader-text"><?php _e('Filter by Customer Status', 'woo-better-customer-list' ); ?></label>
			<select name="cusstatus" id="cus-status">
				<option <?php if (!$customerstatus) { echo 'selected="selected"'; } ?> value=""><?php _e('Active/Inactive', 'woo-better-customer-list' ); ?></option>
				<option <?php if ($customerstatus == 'Active') { echo 'selected="selected"'; } ?> value="Active"><?php _e('Active', 'woo-better-customer-list' ); ?></option>
				<option <?php if ($customerstatus == 'Inactive') { echo 'selected="selected"'; } ?> value="Inactive"><?php _e('Inactive', 'woo-better-customer-list' ); ?></option>
			</select>
			<label for="cus-average" class="screen-reader-text"><?php _e('Filter by if Average Set', 'woo-better-customer-list' ); ?></label>
			<select name="cusaverage" id="cus-average">
				<option <?php if (!$customeraverage) { echo 'selected="selected"'; } ?> value=""><?php _e('Average Set/Not Set', 'woo-better-customer-list' ); ?></option>
				<option <?php if ($customeraverage == 'set') { echo 'selected="selected"'; } ?> value="set"><?php _e('Average Set', 'woo-better-customer-list' ); ?></option>
				<option <?php if ($customeraverage == 'notset') { echo 'selected="selected"'; } ?> value="notset"><?php _e('Average Not Set', 'woo-better-customer-list' ); ?></option>
			</select>
			<label for="cus-ordered" class="screen-reader-text"><?php _e('Filter by if have Ordered', 'woo-better-customer-list' ); ?></label>
			<select name="cusordered" id="cus-ordered">
				<option <?php if (!$customerordered) { echo 'selected="selected"'; } ?> value=""><?php _e('Ordered/Not Ordered', 'woo-better-customer-list' ); ?></option>
				<option <?php if ($customerordered == 'ordered') { echo 'selected="selected"'; } ?> value="ordered"><?php _e('Ordered', 'woo-better-customer-list' ); ?></option>
				<option <?php if ($customerordered == 'notordered') { echo 'selected="selected"'; } ?> value="notordered"><?php _e('Not Ordered', 'woo-better-customer-list' ); ?></option>
			</select>
			<input type="submit" id="filter-submit" class="button" value="<?php _e('Filter', 'woo-better-customer-list' ); ?>">
			<a href="admin.php?page=blz-bcl-list-customers" class="button" style="display: inline-block; margin: 0;"><?php _e('Reset', 'woo-better-customer-list' ); ?></a>
		</form>
		<?php
		if ( $page_links ) {
			echo '<div class="tablenav-pages">' . $page_links . '</div>';
		}

		if ( $order == 'ASC' ) {
			$orderclass = 'asc';
			$orderlink = 'DESC';
		} else {
			$orderclass = 'desc';
			$orderlink = 'ASC';
		}


		?>
	</div>

	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col" id="full_name" class="manage-column column-full_name <?php echo ('display_name' == $orderby) ? 'sorted ' : 'sortable '; echo $order; ?>">
					<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&orderby=display_name&order=<?php echo $orderlink; ?>">
						<span><?php _e('Full Name', 'woo-better-customer-list' ); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th><?php _e('Customer Status', 'woo-better-customer-list' ); ?></th>
				<th><?php _e('Email', 'woo-better-customer-list' ); ?></th>
				<th scope="col" id="avg_order" class="manage-column column-avg_order <?php echo ('customer_average' == $metakey) ? 'sorted ' : 'sortable '; echo $orderclass; ?>">
					<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&orderby=meta_value_num&metakey=customer_average&order=<?php echo $orderlink; ?>">
						<span><?php _e('Avg Order Rate', 'woo-better-customer-list' ); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th><?php _e('Last Order', 'woo-better-customer-list' ); ?></th>
				<th scope="col" id="total_orders" class="manage-column column-total_orders <?php echo ('_order_count' == $metakey) ? 'sorted ' : 'sortable '; echo $orderclass; ?>">
					<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&orderby=meta_value_num&metakey=_order_count&order=<?php echo $orderlink; ?>">
						<span><?php _e('Total Orders', 'woo-better-customer-list' ); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" id="total_spend" class="manage-column column-total_spend <?php echo ('_money_spent' == $metakey) ? 'sorted ' : 'sortable '; echo $order; ?>">
					<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&orderby=meta_value_num&metakey=_money_spent&order=<?php echo $orderlink; ?>">
						<span><?php _e('Total Spend', 'woo-better-customer-list' ); ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php

			if (empty($listusers)) {
				echo '<tr><td colspan="7">';
				_e('No customers', 'woo-better-customer-list' );
				echo '</td></tr>';
			} else {

				$all_orders = $wpdb->get_results(
					"
					SELECT DISTINCT {$wpdb->prefix}posts.post_date_gmt, {$wpdb->prefix}posts.ID, {$wpdb->prefix}postmeta.meta_value
					FROM {$wpdb->prefix}posts
					INNER JOIN {$wpdb->prefix}postmeta
					ON ( {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id )
					WHERE 1=1
					AND {$wpdb->prefix}postmeta.meta_key = '_customer_user'
					AND {$wpdb->prefix}posts.post_type IN ('shop_order', 'shop_order_refund')
					ORDER BY {$wpdb->prefix}posts.post_date_gmt DESC
					"
				);

				foreach ( $listusers as $user ) {

					$bcl = new WooBLZBCL( $user->ID );

					$cusorders = array();

					foreach ($all_orders as $al) {
						if ($user->ID == $al->meta_value) {
							$cusorders[$al->ID] = array('postdate' => $al->post_date_gmt, 'ID' => $al->ID);
						}
					}


					$i = 1;
					$len = count($cusorders);
					$firstorderdate = '';
					$lastorderdate = '';
					$lastordertext = '';
					$ordersspend = get_woocommerce_currency_symbol().'0';
					$orderstotals = 0;
					$cusstatus = __('Inactive', 'woo-better-customer-list' );
					$otcal = 0;
					$oscal = 0;

					foreach ($cusorders as $co) {

						if ($i == 1) {
							$cusstatus = $bcl->blz_bcl_get_customer_status($co['postdate']);

							$lastorderdate = $co['postdate'];

							$lod = strtotime($lastorderdate);
							$lodformat = date('j F Y',$lod);

							$lastordertext = '<a href="' . admin_url( 'post.php?post=' . $co['ID'] . '&action=edit' ) . '" target="_blank"> #'.$co['ID'].'</a> - '.$lodformat;


						} elseif ($i == $len) {
							$firstorderdate = $co['postdate'];
						}

						$i++;
					}

					$average = $bcl->blz_bcl_get_order_average($firstorderdate, $lastorderdate, $len);

					if (empty($lastordertext)) {
						$lastordertext = __('No Orders', 'woo-better-customer-list' );
						$oscal = 1;
						$otcal = 1;
					}

					$count = get_user_meta( $user->ID, '_order_count', true );

					if ($oscal == 1) {
						$orderstotal = 0;
					} else {
						if ( '' === $count ) {
							$orderstotal = '<div class="blzbcluserot" data-user="'.$user->ID.'"><span style="vertical-align: top;">'.__('Calculating', 'woo-better-customer-list' ).'</span> <img src="/wp-admin/images/spinner.gif"></div>';
						} else {
							$orderstotal = absint( $count );
						}
					}

					$spent = get_user_meta( $user->ID, '_money_spent', true );

					if ($otcal == 1) {
						$ordersspend = get_woocommerce_currency_symbol().'0';
					} else {
						if ( '' === $spent ) {
							$ordersspend = '<div class="blzbcluseros" data-user="'.$user->ID.'"><span style="vertical-align: top;">'.__('Calculating', 'woo-better-customer-list' ).'</span> <img src="/wp-admin/images/spinner.gif"></div>';
						} else {
							$ordersspend = get_woocommerce_currency_symbol().wc_format_decimal( $spent, 2 );
						}
					}

					$firstname = get_user_meta( $user->ID, 'billing_first_name', true );
					$lastname = get_user_meta( $user->ID, 'billing_last_name', true );

					if (isset($user->first_name) && !empty($user->first_name)) {
						$fullname = $user->first_name.' '.$user->last_name;
					} elseif (isset($firstname) && !empty($firstname)) {
						$fullname = $firstname.' '.$lastname;
					} else {
						$fullname = $user->user_login;
					}

					update_user_meta($user->ID, 'customer_status', $cusstatus);

					echo '<tr>
					<td><a href="'.admin_url().'user-edit.php?user_id='.$user->ID.'">' . esc_html( $fullname ) . '</a></td>
					<td>'.$cusstatus.'</td>
					<td>' . esc_html( $user->user_email ) . '</td>
					<td>'.$average.'</td>
					<td>'.$lastordertext.'</td>
					<td style="vertical-align: middle;">'.$orderstotal.'</td>
					<td style="vertical-align: middle;">'.$ordersspend.'</td>
					</tr>';
				}
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td><?php _e('Full Name', 'woo-better-customer-list' ); ?></td>
				<td><?php _e('Customer Status', 'woo-better-customer-list' ); ?></td>
				<td><?php _e('Email', 'woo-better-customer-list' ); ?></td>
				<td><?php _e('Avg Order Rate', 'woo-better-customer-list' ); ?></td>
				<td><?php _e('Last Order', 'woo-better-customer-list' ); ?></td>
				<td><?php _e('Total Orders', 'woo-better-customer-list' ); ?></td>
				<td><?php _e('Total Spend', 'woo-better-customer-list' ); ?></td>
			</tr>
		</tfoot>
	</table>
    <p style="float: right;"><?php _e('Thank you for using Better Customer List for WooCommerce - If you find the plugin useful be sure to leave us a <a href="https://wordpress.org/support/plugin/woo-better-customer-list/reviews/#new-post" target="_blank">review here</a>.', 'woo-better-customer-list' ); ?></p>
</div>
