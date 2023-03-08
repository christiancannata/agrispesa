<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class StoreApps_Marketplace {

	public static function init() {
		?>
		<style type="text/css">
			.update-nag,
			.error,
			.updated,
			#sm_promo_msg,
			#wpfooter {
				display: none !important;
			}
			.wrap.about-wrap.sm-marketplace {
				margin: 0 auto;
				max-width: 80%;
			}
			body {
				background-color: white;
			}
			.sm-marketplace .addons-banner-block-items {
				display: flex;
				height: 22em;
				-webkit-box-orient: horizontal;
				-webkit-box-direction: normal;
				-webkit-flex-direction: row;
				flex-direction: row;
				-webkit-flex-wrap: wrap;
				flex-wrap: wrap;
				-webkit-justify-content: space-around;
				justify-content: space-around;
				margin: 0 -10px 2em -10px;
			}
			.sm-marketplace .addons-banner-block-item {
				border: 1px solid #e6e6e6;
				border-radius: 3px;
				-webkit-box-flex: 1;
				-webkit-flex: 1;
				flex: 1;
				margin: 1em;
				min-width: 200px;
				width: 30%
			}
			.sm-marketplace .addons-banner-block-item-icon {
			    background: #f7f7f7;
			    height: 143px
			}
			.sm-marketplace .addons-banner-block-item-content {
				display: -webkit-box;
				display: -webkit-flex;
				display: flex;
				-webkit-box-orient: vertical;
				-webkit-box-direction: normal;
				-webkit-flex-direction: column;
				flex-direction: column;
				height: 184px;
				-webkit-box-pack: justify;
				-webkit-justify-content: space-between;
				justify-content: space-between;
				padding: 24px;
			}
			.sm-marketplace .addons-banner-block-item-content h3 {
				margin-top: 0
			}
			.sm-marketplace .addons-banner-block-item-content h3 sup {
				color: red;
				opacity: 0.7;
			}
			.sm-marketplace .addons-banner-block-item-content p {
			    margin: 0 0 auto;
			    min-height: 10em;
			}
			.sm-marketplace .addons-wcs-banner-block-image .addons-img {
			    max-height: 86px;
			    max-width: 97px
			}
			.sm-marketplace .addons-banner-block-item-icon {
				display: -webkit-box;
				display: -webkit-flex;
				display: flex;
				-webkit-box-pack: center;
				-webkit-justify-content: center;
				justify-content: center;
			}
			.sm-marketplace .addons-button {
				border-radius: 3px;
				cursor: pointer;
				display: block;
				height: 37px;
				line-height: 37px;
				text-align: center;
				text-decoration: none;
				width: 124px
			}
			.sm-marketplace .addons-button-solid {
				background-color: #4fad43;
				color: #fff;
			}
			.sm-marketplace .addons-banner-block-item-icon img {
				height: 62px;
				width: 62px;
			}
			.sm-marketplace .addons-button-solid:hover {
				color: #fff;
				opacity: .8
			}
			.sm-marketplace .addons-banner-block-item-content h3 {
				font-weight: bold !important;
				font-size: 1.5em !important;
			}
			.sm-marketplace .addons-banner-block-item-content p {
				color: #484c51 !important;
			}
			.sm-marketplace .products-header {
				text-align: center;
				padding: 0 0 2em;
			}
			.sm-marketplace .products-header h1 {
				margin: 1em;
				color: #484c51 !important;
			}
			.sm-marketplace .products-header h2 {
				margin: 1em;
				font-size: 2em;
			}
			.sm-marketplace .page-description {
				margin-bottom: 1.6em;
			}
			.sm-marketplace .intro {
				text-align: center;
				font-size: 1.416em;
				letter-spacing: -0.018em;
				color: rgba(0, 0, 0, 0.75); 
			}
			.clr-a {
				color: #f42267 !important;
			}
			.sm-marketplace .button.button-hero {
				color: #FFF!important;
				border-color: #5850ec !important;
				background: #5850ec!important;
				box-shadow: 0 1px 0 #5850ec;
				font-size: 1em;
				font-weight: bold;
			}
		</style>

		<div class="wrap about-wrap sm-marketplace">
			<header class="products-header">
				<h1 class="products-header__title page-title">Best WooCommerce Plugins</h1>
				<div class="page-description">
					<h2 class="clr-b">Get reliable WooCommerce extensions to sell more, manage your store, retain more customers and make better decisions.</h2>
					<p class="intro">More than 250,000 people use our plugins. StoreApps’ WooCommerce solutions are easy to setup and deliver quick results.</p>
				</div>
			</header>
			<div class="addons-banner-block-items">
				<div class="addons-banner-block-item">
					<!-- <div class="addons-banner-block-item-icon">
						<img class="addons-img" src="https://d3t0oesq8995hv.cloudfront.net/add-ons/generic.png">
					</div> -->
					<div class="addons-banner-block-item-content">
						<h3>Smart Offers</h3>
						<p>Grow your sales on autopilot. Create powerful WooCommerce upsells, downsells, cross-sells, BOGO, order bumps, one-time offers, backend offers, <strong>sales funnels</strong> and more. <strong>Measure offers, optimize and target them</strong> on your website to increase conversions & maximize profits.</p>
						<a class="addons-button addons-button-solid" href="https://www.storeapps.org/product/smart-offers/?utm_source=sm&utm_medium=in_app_marketplace&utm_campaign=in_app_marketplace" target="_blank" rel="noopener">
						<!-- From: $99 -->
						Learn more
						</a>
					</div>
				</div>
				<div class="addons-banner-block-item">
					<!-- <div class="addons-banner-block-item-icon">
						<img class="addons-img" src="https://d3t0oesq8995hv.cloudfront.net/add-ons/generic.png">
					</div> -->
					<div class="addons-banner-block-item-content">
					<h3>Offermative</h3>
						<p>A plugin for dynamic discount pricing, related product recommendations, upsells and funnels for WooCommerce.</p>
						<a class="addons-button addons-button-solid" href="https://wordpress.org/plugins/offermative-discount-pricing-related-products-upsell-funnels-for-woocommerce/" target="_blank" rel="noopener">Learn more</a>
					</div>
				</div>
				<div class="addons-banner-block-item">
					<!-- <div class="addons-banner-block-item-icon">
						<img class="addons-img" src="https://d3t0oesq8995hv.cloudfront.net/add-ons/generic.png">
					</div> -->
					<div class="addons-banner-block-item-content">
						<h3>Bulk Variations Manager</h3>
						<p>Quickly create any combination of product variations from attributes and bulk update prices using differential pricing.</p>
						<a class="addons-button addons-button-solid" href="https://www.storeapps.org/product/bulk-variations-manager/?utm_source=sm&utm_medium=in_app_marketplace&utm_campaign=in_app_marketplace" target="_blank" rel="noopener">
						<!-- From: $49 -->
						Learn more
						</a>
					</div>
				</div>
			</div>
			<p style="text-align: center;">
				<a class="button button-hero" href="https://www.storeapps.org/woocommerce-plugins/?utm_source=sm&utm_medium=in_app_marketplace&utm_campaign=in_app_marketplace" target="_blank" rel="noopener">View all 20+ plugins</a>
			</p>
		</div>
		<?php
	}
}

new StoreApps_Marketplace();
