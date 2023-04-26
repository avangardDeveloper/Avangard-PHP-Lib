<?php

namespace OrangeDataClient\DataObjects;

use Box\BaseBox;
use Box\DataObjects\BaseAuth;
use Box\GenerateBoxAuth;
use Box\Orangedata;

class OrangeDataAuth extends BaseAuth implements GenerateBoxAuth
{
    /**
     * Путь к rsa_2048_private_key.pem
     *
     * @var string
     */
    private $signPKey;

    /**
     * Путь к client.key
     *
     * @var string
     */
    private $sslClientKey;

    /**
     * Путь к client.crt
     *
     * @var string
     */
    private $sslClientCrt;

    /**
     * Пароль от сертификата client.crt
     *
     * @var string
     */
    private $sslClientCrtPass;

    /**
     * Путь к cacert.pem
     *
     * @var string
     */
    private $sslCaCert;

    public function __construct(
        $sno,
        $vat,
        $groupCode,
        $inn,
        $payment_object,
        $payment_method,
        $signPKey,
        $sslClientKey,
        $sslClientCrt,
        $sslClientCrtPass,
        $sslCaCert,
        $testMode = false
    )
    {
        parent::__construct(
            BaseBox::ORANGEDATA,
            new OrangeDataCompany($sno, $vat, $groupCode, $inn),
            $payment_object,
            $payment_method,
            $testMode
        );

        $this->signPKey = $signPKey;
        $this->sslClientKey = $sslClientKey;
        $this->sslClientCrt = $sslClientCrt;
        $this->sslClientCrtPass = $sslClientCrtPass;
        $this->sslCaCert = $sslCaCert;
    }

    public static function fromArray($authArray)
    {
        return new OrangeDataAuth(
            $authArray['sno'],
            $authArray['vat'],
            $authArray['group_code'],
            $authArray['inn'],
            $authArray['payment_object'],
            $authArray['payment_method'],
            $authArray['signPKey'],
            $authArray['sslClientKey'],
            $authArray['sslClientCrt'],
            $authArray['sslClientCrtPass'],
            $authArray['sslCaCert']
        );
    }

    /**
     * @return string
     */
    public function getSignPKey()
    {
        return $this->signPKey;
    }

    /**
     * @return string
     */
    public function getSslClientKey()
    {
        return $this->sslClientKey;
    }

    /**
     * @return string
     */
    public function getSslClientCrt()
    {
        return $this->sslClientCrt;
    }

    /**
     * @return string
     */
    public function getSslClientCrtPass()
    {
        return $this->sslClientCrtPass;
    }

    /**
     * @return string
     */
    public function getSslCaCert()
    {
        return $this->sslCaCert;
    }

    public static function getAuthParams()
    {
        return [
            'box' => [
                'type' => BaseBox::ORANGEDATA,
                'name' => 'OrangeData'
            ],
            'inn' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'ИНН',
                'validation' => 'Введите ИНН компании'
            ],
            'signPKey' => [
                'type' => self::INPUT_TYPE_FILE,
                'placeholder' => 'rsa_2048_private_key.pem',
                'validation' => 'Прикрепите файл rsa_2048_private_key.pem'
            ],
            'sslClientKey' => [
                'type' => self::INPUT_TYPE_FILE,
                'placeholder' => 'client.key',
                'validation' => 'Прикрепите файл client.key'
            ],
            'sslClientCrt' => [
                'type' => self::INPUT_TYPE_FILE,
                'placeholder' => 'client.crt',
                'validation' => 'Прикрепите файл client.crt'
            ],
            'sslClientCrtPass' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'Пароль сертификата',
                'validation' => 'Введите пароль сертификата'
            ],
            'sslCaCert' => [
                'type' => self::INPUT_TYPE_FILE,
                'placeholder' => 'cacert.pem',
                'validation' => 'Прикрепите файл cacert.pem'
            ],
            'sno' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Система налогообложения',
                'validation' => 'Выберите систему налогообложения компании',
                'options' => Orangedata::getTaxationSystems(),
            ],
            'vat' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Налоговая ставка',
                'validation' => 'Выберите налоговую ставку компании',
                'options' => Orangedata::getVats(),
            ],
            'payment_method' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Способ расчёта',
                'validation' => 'Выберите способ расчёта',
                'options' => Orangedata::getPaymentMethods(),
            ],
            'payment_object' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Предмет расчёта',
                'validation' => 'Выберите предмет расчёта',
                'options' => Orangedata::getPaymentObjects(),
            ],
        ];
    }
}