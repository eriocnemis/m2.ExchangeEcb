<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\ExchangeEcb\Model\Currency;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Store\Model\ScopeInterface;

/**
 * Import gateway
 */
class Gateway
{
    /**
     * Reference rates url
     *
     * @var string
     */
    const ECB_URL = 'https://www.ecb.int/stats/eurofxref/eurofxref-daily.xml';

    /**
     * Http client factory
     *
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * Core scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Initialize gateway
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ZendClientFactory $httpClientFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ZendClientFactory $httpClientFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * Retrieve service response
     *
     * @return \Zend_Http_Response
     */
    public function getResponse()
    {
        return $this->getClient()->setUri(self::ECB_URL)
            ->setConfig($this->getConfig())
            ->request(\Zend_Http_Client::GET);
    }

    /**
     * Retrieve http client
     *
     * @return ZendClient
     */
    protected function getClient()
    {
        return $this->httpClientFactory->create();
    }

    /**
     * Retrieve http client config
     *
     * @return array
     */
    protected function getConfig()
    {
        return [
            'timeout' => $this->scopeConfig->getValue(
                'currency/ecb/timeout',
                ScopeInterface::SCOPE_STORE
            ),
        ];
    }
}
