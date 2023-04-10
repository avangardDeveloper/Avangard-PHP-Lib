<?php
/**
 * Copyright Ignatev Aleksander. Avangard (c) 2023.
 */

Namespace Atol102\Services\Request;

abstract class BaseServiceRequest
{
    const TEST_REQUEST_URL = 'https://testonline.atol.ru/possystem/v5/';
    const REQUEST_URL = 'https://online.atol.ru/possystem/v5/';

    /**
     * Получить url для запроса
     *
     * @param bool $test
     * @return string
     */
    abstract public function getRequestUrl($test = false);

    /**
     * Получить базовый url для запроса
     * @param bool $test
     * @return string
     */
    public function getBaseUrl($test = false) {
        return $test ? self::TEST_REQUEST_URL : self::REQUEST_URL;
    }

    /**
     * Получить параметры, сгенерированные командой
     * @return array
     */
    public function getParameters()
    {
        $filledvars = array();
        foreach (get_object_vars($this) as $name => $value) {
            if ($value) {
                $filledvars[$name] = (string)$value;
            }
        }

        return $filledvars;
    }
}
