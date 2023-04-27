<?php
/**
 * Copyright Ignatev Aleksander. Avangard (c) 2023.
 */

Namespace AtolV5\Services\Request;

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
        return parent::getBaseUrl($test) . 'getToken?' . http_build_query(parent::getParameters());
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
