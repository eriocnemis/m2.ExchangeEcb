<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\ExchangeEcb\Model\Currency;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Laminas\Http\Response;
use Laminas\Http\Client;

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
     * Http client
     *
     * @var Client
     */
    protected $httpClient;

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
     * @param Client $httpClient
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Client $httpClient
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->httpClient = $httpClient;
    }

    /**
     * Retrieve service response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->getClient()->setUri(self::ECB_URL)
            ->setOptions($this->getOptions())
            ->send();
    }

    /**
     * Retrieve http client
     *
     * @return Client
     */
    protected function getClient()
    {
        return $this->httpClient;
    }

    /**
     * Retrieve http client options
     *
     * @return mixed[]
     */
    protected function getOptions()
    {
        return [
            'timeout' => $this->scopeConfig->getValue(
                'currency/ecb/timeout',
                ScopeInterface::SCOPE_STORE
            ),
        ];
    }
}
