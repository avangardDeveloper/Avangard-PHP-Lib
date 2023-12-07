<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Avangard;

use Avangard\Api\ApiVersion4;

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
     * Type of box
     */
    const NONEBOX = 0;
    /**
     * Type of box
     */
    const ATOLBOX = 1;
    /**
     * Type of box
     */
    const ORANGEDATABOX = 2;

    /**
     * ApiClient constructor.
     *
     * @param $shop_id
     * @param $shop_password
     * @param $shop_sign
     * @param $server_sign
     * @param int $boxType
     * @param array $boxAuth
     * @param string $proxy
     */
    public function __construct($shop_id, $shop_password, $shop_sign, $server_sign, $boxType = self::NONEBOX, $boxAuth = array(), $proxy = null)
    {
        $this->request = new ApiVersion4($shop_id, $shop_password, $shop_sign, $server_sign, $boxType, $boxAuth, $proxy);
    }

    /**
     * Get API version's
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
        $ver = '1.8.1';
        return "Library version $ver. Avangard (c) 2023.";
    }
}
