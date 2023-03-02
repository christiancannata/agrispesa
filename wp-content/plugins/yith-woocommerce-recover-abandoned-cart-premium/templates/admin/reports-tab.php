<div id="yith_woocommerce_recover_abandoned_cart_reports" class="yith-plugin-fw yit-admin-panel-container yith-plugin-fw-panel-custom-tab-container">
	<div class="yit-admin-panel-content-wrap">
		<div id="plugin-fw-wc">
			<div id="ywrac-reports-content" class="ywrac-admin-tab">
				<div class="ywrac-reports-section ywrac-reports-recovered">
					<div class="ywrac-report-box">
						<div class="report-title">
							<span class="report-title-text"><?php esc_html_e( 'Recovered Carts and Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
							<img class="ywrac-report-icon" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/orders.svg'; ?>" />
						</div>
						<div class="ywrac-report-main-data">
							<span><?php echo esc_html( $recovered_carts ); ?></span>
						</div>
						<div class="details">
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Recovered carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo esc_html( $total_recovered_carts ); ?></span>
							</div>
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Recovered orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo esc_html( $total_recovered_pending_orders ); ?></span>
							</div>
						</div>
					</div>
					<div class="ywrac-report-box">
						<div class="report-title">
							<span class="report-title-text"><?php esc_html_e( 'Rate Conversion', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
							<img class="ywrac-report-icon" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/conversion.svg'; ?>" />
						</div>
						<div class="ywrac-report-main-data">
							<span><?php echo esc_html( $rate_conversion ); ?> %</span>
						</div>
						<div class="details">
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Rate cart conversion', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo esc_html( $rate_cart_conversion ); ?> %</span>
							</div>
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Pending orders conversion', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo esc_html( $rate_order_conversion ); ?> %</span>
							</div>
						</div>
					</div>
					<div class="ywrac-report-box">
						<div class="report-title">
							<span class="report-title-text"><?php esc_html_e( 'Total Amount Recovered', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
							<img class="ywrac-report-icon" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/amount.svg'; ?>" />
						</div>
						<div class="ywrac-report-main-data">
							<span><?php echo wp_kses_post( wc_price( $total_amount ) ); ?></span>
						</div>
						<div class="details">
							<div class="detail-line featured">
								<span class="report-title-text"><?php esc_html_e( 'Total carts recovered', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo wp_kses_post( wc_price( $total_cart_amount ) ); ?></span>
							</div>
							<div class="detail-line featured">
								<span class="report-title-text"><?php esc_html_e( 'Total orders recovered', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo wp_kses_post( wc_price( $total_order_amount ) ); ?></span>
							</div>
						</div>
					</div>
				</div>

				<div class="ywrac-reports-section ywrac-reports-totals">
					<div class="ywrac-report-box">
						<div class="report-title">
							<span class="report-title-text"><?php esc_html_e( 'Abandoned Cart and Pending Orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
							<img class="ywrac-report-icon" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/cart.svg'; ?>" />
						</div>
						<div class="ywrac-report-main-data">
							<span><?php echo esc_html( $abandoned_carts_counter ); ?></span>
						</div>
						<div class="details">
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Abandoned carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo esc_html( $total_abandoned_carts ); ?></span>
							</div>

							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Pending orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php echo esc_html( $total_pending_orders ); ?> </span>
							</div>
						</div>
					</div>
					<div class="ywrac-report-box">
						<div class="report-title">
							<span class="report-title-text"><?php esc_html_e( 'Emails Sent', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
							<img class="ywrac-report-icon" src="<?php echo YITH_YWRAC_ASSETS_URL . '/images/mail.svg'; ?>" />
						</div>
						<div class="ywrac-report-main-data">
							<span><?php printf( esc_html( __( '%1$d', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $email_sent_counter ), esc_html( $email_clicks_counter ) ); ?></span>
						</div>
						<div class="details">
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Emails for abandoned carts', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php printf( esc_html( __( '%1$d (%2$d Clicks)', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $email_sent_cart_counter ), esc_html( $email_cart_clicks_counter ) ); ?></span>
							</div>
							<div class="detail-line">
								<span class="report-title-text"><?php esc_html_e( 'Emails for pending orders', 'yith-woocommerce-recover-abandoned-cart' ); ?></span>
								<span class="ywrac-report-data"><?php printf( esc_html( __( '%1$d (%2$d Clicks)', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $email_sent_order_counter ), esc_html( $email_order_clicks_counter ) ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
