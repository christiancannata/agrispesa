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
namespace FSVendor\Monolog\Handler;

use FSVendor\Monolog\Logger;
use FSVendor\Monolog\Formatter\NormalizerFormatter;
use FSVendor\Monolog\Formatter\FormatterInterface;
use FSVendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \FSVendor\Monolog\Handler\AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    public function __construct(\FSVendor\Doctrine\CouchDB\CouchDBClient $client, $level = \FSVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter() : \FSVendor\Monolog\Formatter\FormatterInterface
    {
        return new \FSVendor\Monolog\Formatter\NormalizerFormatter();
    }
}
