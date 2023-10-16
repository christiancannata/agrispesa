<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FSVendor\Monolog\Formatter;

use FSVendor\Monolog\Logger;
/**
 * Formats a log message according to the ChromePHP array format
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ChromePHPFormatter implements \FSVendor\Monolog\Formatter\FormatterInterface
{
    /**
     * Translates Monolog log levels to Wildfire levels.
     *
     * @var array<int, 'log'|'info'|'warn'|'error'>
     */
    private $logLevels = [\FSVendor\Monolog\Logger::DEBUG => 'log', \FSVendor\Monolog\Logger::INFO => 'info', \FSVendor\Monolog\Logger::NOTICE => 'info', \FSVendor\Monolog\Logger::WARNING => 'warn', \FSVendor\Monolog\Logger::ERROR => 'error', \FSVendor\Monolog\Logger::CRITICAL => 'error', \FSVendor\Monolog\Logger::ALERT => 'error', \FSVendor\Monolog\Logger::EMERGENCY => 'error'];
    /**
     * {@inheritDoc}
     */
    public function format(array $record)
    {
        // Retrieve the line and file if set and remove them from the formatted extra
        $backtrace = 'unknown';
        if (isset($record['extra']['file'], $record['extra']['line'])) {
            $backtrace = $record['extra']['file'] . ' : ' . $record['extra']['line'];
            unset($record['extra']['file'], $record['extra']['line']);
        }
        $message = ['message' => $record['message']];
        if ($record['context']) {
            $message['context'] = $record['context'];
        }
        if ($record['extra']) {
            $message['extra'] = $record['extra'];
        }
        if (\count($message) === 1) {
            $message = \reset($message);
        }
        return [$record['channel'], $message, $backtrace, $this->logLevels[$record['level']]];
    }
    /**
     * {@inheritDoc}
     */
    public function formatBatch(array $records)
    {
        $formatted = [];
        foreach ($records as $record) {
            $formatted[] = $this->format($record);
        }
        return $formatted;
    }
}
