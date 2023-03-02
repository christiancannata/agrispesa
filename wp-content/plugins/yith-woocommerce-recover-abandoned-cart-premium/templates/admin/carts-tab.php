<div id="yith_woocommerce_recover_abandoned_cart_carts" class="yith-plugin-fw yit-admin-panel-container yith-plugin-fw-panel-custom-tab-container">
	<div id="ywrac-carts-content" class="wrap">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Abandoned Carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></h1>
					<form method="post">
						<input type="hidden" name="page" value="yith_woocommerce_recover_abandoned_cart" />
					</form>
					<?php $this->cpt_obj->prepare_items(); ?>
					<?php if ( $this->cpt_obj->has_items() ) : ?>
						<form method="post">
							<?php $this->cpt_obj->display(); ?>
						</form>
					<?php else : ?>
						<div class="ywrac-admin-no-posts">
							<div class="ywrac-admin-no-posts-container">
								<div class="ywrac-admin-no-posts-logo"><img width="100" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/abandoned-cart.svg'; ?>"></div>
								<div class="ywrac-admin-no-posts-text">
									<span>
										<strong><?php esc_html_e( 'You have no abandoned carts yet.', 'yith-woocommerce-recover-abandoned-cart' ); ?></strong>
									</span>
								</div>
								<div class="ywrac-admin-no-posts-text">
									<span><?php esc_html_e( "But don't worry, soon something cool will appear here.", 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
