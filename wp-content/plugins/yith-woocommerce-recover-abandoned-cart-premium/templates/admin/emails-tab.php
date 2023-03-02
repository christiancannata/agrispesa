<div id="yith_woocommerce_recover_abandoned_cart_emails" class="yith-plugin-fw yit-admin-panel-container yith-plugin-fw-panel-custom-tab-container">
    <div id="ywrac-emails-content" class="wrap">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Email templates', 'yith-woocommerce-recover-abandoned-cart' ); ?></h1>
                    <a class="page-title-action" href="<?php echo esc_url( add_query_arg( 'post_type', YITH_WC_Recover_Abandoned_Cart_Email()->post_type_name, admin_url( 'post-new.php' ) ) ); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'yith-woocommerce-recover-abandoned-cart' ); ?></a>
					<form method="post">
						<?php
						$this->cpt_obj_emails->prepare_items();
						$this->cpt_obj_emails->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
