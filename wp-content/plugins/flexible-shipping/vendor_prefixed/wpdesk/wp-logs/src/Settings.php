<?php

namespace FSVendor\WPDesk\Logger;

use Psr\Log\LogLevel;
final class Settings
{
    /** @var string */
    public $level = \Psr\Log\LogLevel::DEBUG;
    /** @var bool */
    public $use_wc_log = \true;
    /** @var bool */
    public $use_wp_log = \true;
    /**
     * @param string $level
     * @param bool   $use_wc_log
     * @param bool   $use_wp_log
     */
    public function __construct(string $level = \Psr\Log\LogLevel::DEBUG, bool $use_wc_log = \true, bool $use_wp_log = \true)
    {
        $this->level = $level;
        $this->use_wc_log = $use_wc_log;
        $this->use_wp_log = $use_wp_log;
    }
}
