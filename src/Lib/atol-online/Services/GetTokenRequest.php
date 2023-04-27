<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace AtolV4\Services;

class GetTokenRequest extends BaseServiceRequest
{
    /** @var string */
    protected $login;
    /** @var string */
    protected $pass;

    /**
     * @inheritdoc
     */
    public function getRequestUrl($test = false)
    {
        return ($test ? self::TEST_REQUEST_URL : self::REQUEST_URL) . 'getToken?' . http_build_query(parent::getParameters());
    }

    /**
     * Получить токен для сессии
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->pass = $password;
    }

}
