<?php

namespace OrangeDataClient\DataObjects;

use Box\DataObjects\BaseCompany;

class OrangeDataCompany extends BaseCompany
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

    public function __construct($sno, $vat, $groupCode, $inn)
    {
        parent::__construct($sno, $vat);

        $this->groupCode = $groupCode;
        $this->inn = $inn;
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
    public function getGroupCode()
    {
        return $this->groupCode;
    }
}