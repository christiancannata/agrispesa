<?php

namespace GtmEcommerceWoo\Lib\Util;

use GtmEcommerceWoo\Lib\GaEcommerceEntity\Event;
use GtmEcommerceWoo\Lib\GaEcommerceEntity\Item;
use WC_Order;
use WC_Product_Variation;

/**
 * Logic to transform WooCommerce datatypes into GA Ecommerce Events types
 */
class WcTransformerUtil {
	/**
	 * See:
	 * https://woocommerce.github.io/code-reference/classes/WC-Order-Item.html
	 * https://woocommerce.github.io/code-reference/classes/WC-Order-Item-Product.html
	 */
	public function getItemFromOrderItem( $orderItem ): Item {
		$product = $orderItem->get_product();
		$variantProduct = ( $orderItem->get_variation_id() ) ? ( wc_get_product( $orderItem->get_variation_id() ) )->get_name() : '';

		$item = new Item($orderItem->get_name());
		$item->setItemId($product->get_id());
		$item->setPrice($product->get_price());
		$item->setItemVariant($variantProduct);
		$item->setQuantity($orderItem->get_quantity());

		$itemCats = ( $orderItem->get_variation_id() ) ? get_the_terms( $product->get_parent_id(), 'product_cat' ) : get_the_terms( $product->get_id(), 'product_cat' );
		if (is_array($itemCats)) {
			$categories = array_map(
				static function( $category) {
					return $category->name;
				},
				$itemCats
			);
			$item->setItemCategories($categories);
		}

		/**
		 * Allows customizing item object.
		 *
		 * @since 1.8.0
		 */
		return apply_filters('gtm_ecommerce_woo_item', $item, $product);
	}

	/**
	 * See
	 * https://woocommerce.github.io/code-reference/classes/WC-Product.html
	 * https://woocommerce.github.io/code-reference/classes/WC-Product-Simple.html
	 */
	public function getItemFromProduct( $product ): Item {
		$item = new Item($product->get_name());
		$item->setItemId($product->get_id());
		$item->setPrice($product->get_price());


		$productCats = ( $product instanceof WC_Product_Variation )
			? get_the_terms( $product->get_parent_id(), 'product_cat' )
			: get_the_terms( $product->get_id(), 'product_cat' );

		if (is_array($productCats)) {
			$categories = array_map(
				static function( $category) {
					return $category->name; },
				$productCats
			);
			$item->setItemCategories($categories);
		}

		/**
		 * Allows customizing item object.
		 *
		 * @since 1.8.0
		 */
		return apply_filters('gtm_ecommerce_woo_item', $item, $product);
	}

	public function getPurchaseFromOrderId( int $orderId ): Event {
		$order = wc_get_order( $orderId );

		return $this->getPurchaseFromOrder($order);
	}

	public function getPurchaseFromOrder( WC_Order $order): Event {
		$event = new Event('purchase');
		$event->setCurrency($order->get_currency());
		$event->setTransactionId($order->get_order_number());
		$event->setAffiliation(get_bloginfo( 'name' ));
		$event->setValue(number_format( $order->get_total(), 2, '.', '' ));
		$event->setTax(number_format( $order->get_total_tax(), 2, '.', '' ));
		$event->setShipping(number_format( ( $order->get_total_shipping() + $order->get_shipping_tax() ), 2, '.', '' ));
		if ( $order->get_coupon_codes() ) {
			$event->setCoupon(implode( ',', $order->get_coupon_codes() ) );
		}

		$event->setExtraProperty('payment_method', $order->get_payment_method());

		foreach ( $order->get_items() as $key => $orderItem ) {
			$item = $this->getItemFromOrderItem($orderItem);
			$event->addItem($item);
		}

		/**
		 * Allows customizing purchase event object.
		 *
		 * @since 1.8.0
		 */
		return apply_filters('gtm_ecommerce_woo_purchase_event', $event, $order);
	}
}
