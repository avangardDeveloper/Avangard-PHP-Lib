<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

Namespace Atol102\Services\Response;

use stdClass;

abstract class BaseServiceResponse
{

    /** @var int */
    protected $errorCode;

    /** @var string */
    protected $errorDescription;

    public function __construct(stdClass $response)
    {
        foreach (get_object_vars($this) as $name => $value) {
            if (!empty($response->$name)) {
                $this->$name = $response->$name;
            }
        }
    }

    /**
     * Проверка на ошибки в ответе
     * @param array $response
     * @return boolean
     */
    public function isValid()
    {
        return empty($this->errorCode);
    }

    /**
     * Получить код ошибки из ответа
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Получить описание ошибки из ответа
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }
}
