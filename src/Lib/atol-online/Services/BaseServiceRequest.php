<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace AtolV4\Services;

abstract class BaseServiceRequest
{
    const TEST_REQUEST_URL = 'https://testonline.atol.ru/possystem/v4/';
    const REQUEST_URL = 'https://online.atol.ru/possystem/v4/';

    /**
     * Получить url для запроса
     *
     * @param bool $test
     * @return string
     */
    abstract public function getRequestUrl($test = false);

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
