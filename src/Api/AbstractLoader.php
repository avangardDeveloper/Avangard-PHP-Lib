<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Avangard\Api;

use Box\DataObjects\BaseAuth;
use Box\BaseBox;
use Box\BoxFactory;
use GuzzleHttp\Client;

/**
 * Class AbstractLoader
 *
 * @package Avangard\Api
 */
abstract class AbstractLoader
{
    /**
     * Object of Guzzle client
     *
     * @var Client
     */
    protected $client;
    /**
     * Bank shop id
     *
     * @var string
     */
    protected $shop_id;
    /**
     * Bank shop password
     *
     * @var string
     */
    protected $shop_password;
    /**
     * Bank shop signature
     * @var string
     */
    protected $shop_sign;
    /**
     * Bank server signature
     *
     * @var string
     */
    protected $server_sign;

    /**
     * Object of one of the boxes
     *
     * @var BaseBox|null
     */
    protected $box = null;

    /**
     * AbstractLoader constructor.
     *
     * @param $shop_id
     * @param $shop_password
     * @param $shop_sign
     * @param $server_sign
     * @param BaseAuth|null $boxAuth
     * @param $proxy
     */
    public function __construct($shop_id, $shop_password, $shop_sign, $server_sign, $boxAuth, $proxy)
    {
        if (empty($shop_id) ||
            empty($shop_password) ||
            empty($shop_sign) ||
            empty($server_sign)
            ) {
            throw new \InvalidArgumentException(
                "Incorrect arguments in abstract constructor."
            );
        }

        $this->client = new Client((!empty($proxy) ? ['proxy' => $proxy, 'http_errors' => false, 'verify' => false] : ['http_errors' => false, 'verify' => false]));
        $this->shop_id = (string)$shop_id;
        $this->shop_password = (string)$shop_password;
        $this->shop_sign = (string)$shop_sign;
        $this->server_sign = (string)$server_sign;
        $this->box = BoxFactory::createBox($boxAuth, $this->client);
    }

    /**
     * Check box object
     *
     * @return bool
     */
    public function isBox()
    {
        return !empty($this->getBox());
    }

    /**
     * Get box object
     *
     * @return BaseBox|null
     */
    protected function getBox()
    {
        return $this->box;
    }

    /**
     * Get auth data to bank
     *
     * @return array
     */
    protected function getOrderAccess()
    {
        return [
            "SHOP_ID" => $this->shop_id,
            'SHOP_PASSWD' => $this->shop_password
        ];
    }

    /**
     * Send http header
     */
    public function sendResponse()
    {
        header("HTTP/1.1 202 Accepted");
        die();
    }
}