<?php

namespace AtolV4\DataObjects;

use Box\DataObjects\BaseCompany;

class AtolV4Company extends BaseCompany
{
    /**
     * Код кассовой группы
     *
     * @var string
     */
    private $groupCode;

    /**
     * ИНН компании
     *
     * @var string
     */
    private $inn;

    /**
     * Место расчётов компании
     *
     * @var string
     */
    private $payment_address;

    public function __construct($sno, $vat, $groupCode, $inn, $paymentAddress)
    {
        parent::__construct($sno, $vat);

        $this->groupCode = $groupCode;
        $this->inn = $inn;
        $this->payment_address = $paymentAddress;
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return $this->groupCode;
    }

    /**
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @return string
     */
    public function getPaymentAddress()
    {
        return $this->payment_address;
    }
}