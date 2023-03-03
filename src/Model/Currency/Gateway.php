<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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
    private const ECB_URL = 'https://www.ecb.int/stats/eurofxref/eurofxref-daily.xml';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

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
    private function getClient()
    {
        return $this->httpClient;
    }

    /**
     * Retrieve http client options
     *
     * @return mixed[]
     */
    private function getOptions()
    {
        return [
            'timeout' => $this->scopeConfig->getValue(
                'currency/ecb/timeout',
                ScopeInterface::SCOPE_STORE
            ),
        ];
    }
}
