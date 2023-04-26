<?php

namespace AtolV5\DataObjects;

use Box\DataObjects\BaseCompany;

class AtolV5Company extends BaseCompany
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

    /**
     * Email компании
     *
     * @var string
     */
    private $email;

    public function __construct($sno, $vat, $groupCode, $inn, $paymentAddress, $email)
    {
        parent::__construct($sno, $vat);

        $this->groupCode = $groupCode;
        $this->inn = $inn;
        $this->payment_address = $paymentAddress;
        $this->email = $email;
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

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}