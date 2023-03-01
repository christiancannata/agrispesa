<div id="yith_woocommerce_recover_abandoned_cart_recovered" class="yit-admin-panel-container yith-plugin-fw-panel-custom-tab-container">
    <div id="ywrac-recovered-content" class="wrap">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Recovered carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></h1>
                    <?php $this->cpt_obj_orders->prepare_items(); ?>
                    <?php if ( $this->cpt_obj_orders->has_items() ) : ?>
                        <form method="post">
                            <?php $this->cpt_obj_orders->display(); ?>
                        </form>
                    <?php else: ?>
                        <div class="ywrac-admin-no-posts">
                            <div class="ywrac-admin-no-posts-container">
                                <div class="ywrac-admin-no-posts-logo"><img width="100" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/recovered-cart.svg'; ?>"></div>
                                <div class="ywrac-admin-no-posts-text">
                                    <span>
                                        <strong><?php esc_html_e( 'You have no recovered carts yet.', 'yith-woocommerce-recover-abandoned-cart' ); ?></strong>
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
