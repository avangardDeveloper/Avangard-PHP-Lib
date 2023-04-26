<?php

namespace Box\DataObjects;

use Box\BaseBox;

abstract class BaseAuth extends BaseDataObject
{
    /**
     * Тип кассы
     *
     * @var string
     */
    private $boxType;

    /**
     * Информация о компании
     *
     * @var BaseCompany
     */
    private $company;

    /**
     * Предмет расчёта
     *
     * @var string
     */
    private $payment_object;

    /**
     * Способ расчёта
     *
     * @var string
     */
    private $payment_method;

    /**
     * Включён ли тестовый режим
     *
     * @var bool
     */
    private $testMode;

    public function __construct($boxType, $company, $payment_object, $payment_method, $testMode = false)
    {
        $this->boxType = $boxType;
        $this->company = $company;
        $this->payment_object = $payment_object;
        $this->payment_method = $payment_method;
        $this->testMode = $testMode;
    }

    /**
     * @return string
     */
    public function getBoxType()
    {
        return $this->boxType;
    }

    /**
     * @return BaseCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getPaymentObject()
    {
        return $this->payment_object;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * Включён ли тестовый режим
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     * Фабрика для создания объекта авторизации кассы из ранее сохранённого JSON
     *
     * @return null
     */
    static function fromArray($authArray)
    {
        return null;
    }
}