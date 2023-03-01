<div id="yith_woocommerce_recover_abandoned_cart_mailslog" class="yit-admin-panel-container yith-plugin-fw-panel-custom-tab-container">
    <div id="ywrac-mailslog-content" class="wrap">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Email logs', 'yith-woocommerce-recover-abandoned-cart' ); ?></h1>
					<form method="post">
						<input type="hidden" name="page" value="yith_woocommerce_recover_abandoned_cart" />
						<?php $this->cpt_obj_mailslog->search_box( 'search', 'search_id' ); ?>
					</form>
					<form method="post">
						<?php
							$this->cpt_obj_mailslog->prepare_items();
							$this->cpt_obj_mailslog->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
