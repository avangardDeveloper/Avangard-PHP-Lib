<?php

namespace Box;

interface GenerateBox
{
    /**
     * Возвращает все способы расчёта кассы
     *
     * @return array
     */
    public static function getPaymentMethods();

    /**
     * Возвращает все объекты платежа кассы
     *
     * @return array
     */
    public static function getPaymentObjects();

    /**
     * Возвращает все типы налогообложения кассы
     *
     * @return array
     */
    public static function getTaxationSystems();

    /**
     * Возвращает все ставки налогов кассы
     *
     * @return array
     */
    public static function getVats();
}