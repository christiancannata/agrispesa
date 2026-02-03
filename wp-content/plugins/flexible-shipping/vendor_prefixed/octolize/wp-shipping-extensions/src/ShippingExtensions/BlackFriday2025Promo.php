<?php

namespace FSVendor\Octolize\ShippingExtensions;

use FSVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class BlackFriday2025Promo implements Hookable
{
    private const PROMO_CODE = 'bf2025';
    private const PROMO_CONTENT = '<span>BLACK FRIDAY MONTH DEAL: Save 20% on premium bundles!<br>Don\'t hesitate - offer ends November 30th. 🚀 <a href="https://octolize.com/black-friday-sale/?utm_source=plugin&utm_medium=referral&utm_campaign=shipping_extensions_tab_blackfriday">Learn more &#8594;</a></span>';
    private const PROMO_START_DATE = '2025-11-02';
    private const PROMO_END_DATE = '2025-11-30';
    public function hooks()
    {
        add_filter('octolize/shipping-extensions/header-promo', [$this, 'add_promo']);
        add_filter('octolize/shipping-extensions/should-add-badge', [$this, 'should_add_badge'], 10, 2);
        add_action('octolize/shipping-extensions/view-tracking', [$this, 'view_tracking']);
    }
    /**
     * @param ViewPageTracker $tracker
     * @return void
     */
    public function view_tracking($tracker)
    {
        $tracker->update_views(self::PROMO_CODE);
    }
    /**
     * @param bool $should_add_badge
     * @param ViewPageTracker $view_page_tracker
     * @return bool
     */
    public function should_add_badge($should_add_badge, $view_page_tracker)
    {
        if ($this->is_active_promo()) {
            if (($view_page_tracker->get_views(self::PROMO_CODE) ?? 0) < 1) {
                return \true;
            }
        }
        return $should_add_badge;
    }
    /**
     * @param array $promo
     * @return array
     */
    public function add_promo($promo)
    {
        if ($this->is_active_promo()) {
            $promo[self::PROMO_CODE] = self::PROMO_CONTENT;
        }
        return $promo;
    }
    private function is_active_promo(): bool
    {
        $start_date = strtotime(self::PROMO_START_DATE);
        $end_date = strtotime(self::PROMO_END_DATE);
        return current_time('timestamp') < $end_date && current_time('timestamp') > $start_date;
    }
}
