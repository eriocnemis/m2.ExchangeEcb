<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\ExchangeEcb\Model\Currency;

use Magento\Framework\Xml\Parser;

/**
 * Rates container
 */
class Rate
{
    /**
     * Core scope config
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Xml parser
     *
     * @var Parser
     */
    protected $parser;

    /**
     * Exchange rates
     *
     * @var float[]
     */
    protected $rates = [];

    /**
     * Exchange errors
     *
     * @var bool
     */
    protected $error = false;

    /**
     * Initialize import
     *
     * @param Gateway $gateway
     * @param Parser $parser
     */
    public function __construct(
        Gateway $gateway,
        Parser $parser
    ) {
        $this->gateway = $gateway;
        $this->parser = $parser;
    }

    /**
     * Set error
     *
     * @param bool $error
     * @return void
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Retrieve error
     *
     * @return bool
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Retrieve all rates
     *
     * @return float[]
     */
    public function getAllRates()
    {
        return $this->rates;
    }

    /**
     * Retrieve all codes
     *
     * @return string[]
     */
    public function getAllCodes()
    {
        return array_keys($this->rates);
    }

    /**
     * Retrieve rate by code
     *
     * @param string $code
     * @return float
     */
    public function getRateByCode($code)
    {
        return $this->rates[$code];
    }

    /**
     * Load all rates
     *
     * @return $this
     */
    public function load()
    {
        $this->rates = [];
        $response = $this->gateway->getResponse();
        if ($response->isSuccess()) {
            $this->decode($response->getBody());
        } else {
            $this->setError(true);
        }
        return $this;
    }

    /**
     * Retrieve decoded response
     *
     * @param string $xml
     * @return void
     */
    public function decode($xml)
    {
        $parser = $this->parser->loadXML($xml);
        $dom = $this->parser->getDom();
        if (null !== $dom) {
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('gesmes', 'http://www.gesmes.org/xml/2002-08-01');
            $xpath->registerNamespace('e', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

            foreach ($xpath->evaluate('/gesmes:Envelope/e:Cube/e:Cube/e:Cube') as $cube) {
                $this->rates[$cube->getAttribute('currency')] = $cube->getAttribute('rate');
            }
        } else {
            $this->setError(true);
        }
    }
}
