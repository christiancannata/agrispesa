<div id="yith_woocommerce_recover_abandoned_cart_pending_orders" class="yith-plugin-fw  yit-admin-panel-container yith-plugin-fw-panel-custom-tab-container">
    <div id="ywrac-pending-orders-content" class="wrap">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Pending orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></h1>
					<form method="post">
						<?php
						$this->cpt_obj_pending_orders->prepare_items();
						$this->cpt_obj_pending_orders->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
