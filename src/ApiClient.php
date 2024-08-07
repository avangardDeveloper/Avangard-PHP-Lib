<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Avangard;

use Avangard\Api\ApiVersion4;
use Box\DataObjects\BaseAuth;

/**
 * Class ApiClient
 * @package Avangard
 */
class ApiClient
{
    /**
     * Contain object of selected class version api
     *
     * @var ApiVersion4
     */
    public $request;

    /**
     * Type of connection to PS
     */
    const HOST2HOST = 1;
    /**
     * Type of connection to PS
     */
    const POSTFORM = 2;
    /**
     * Type of connection to PS
     */
    const GETURL = 3;

    /**
     * ApiClient constructor.
     *
     * @param $shopId
     * @param $shopPassword
     * @param $shopSign
     * @param $serverSign
     * @param BaseAuth|null $boxAuth
     * @param string $proxy
     */
    public function __construct($shopId, $shopPassword, $shopSign, $serverSign, $boxAuth, $proxy = null)
    {
        $this->request = new ApiVersion4($shopId, $shopPassword, $shopSign, $serverSign, $boxAuth, $proxy);
    }

    /**
     * Get API version
     *
     * @return array
     */
    public static function getApiVersions()
    {
        return ['v4.0'];
    }

    /**
     * Get library version
     *
     * @return string
     */
    public static function getVersion()
    {
        $ver = '3.0.0';
        return "Library version $ver. Avangard (c) 2023.";
    }
}
