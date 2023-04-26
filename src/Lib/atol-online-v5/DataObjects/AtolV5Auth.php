<?php

namespace AtolV5\DataObjects;

use Box\AtolonlineV5;
use Box\BaseBox;
use Box\DataObjects\BaseAuth;
use Box\GenerateBoxAuth;

class AtolV5Auth extends BaseAuth implements GenerateBoxAuth
{
    /**
     * Логин для авторизации АПИ
     *
     * @var string
     */
    private $login;

    /**
     * Пароль для авторизации АПИ
     *
     * @var string
     */
    private $password;

    public function __construct(
        $sno,
        $vat,
        $groupCode,
        $inn,
        $payment_address,
        $payment_object,
        $payment_method,
        $login,
        $password,
        $email,
        $testMode = false
    )
    {
        parent::__construct(
            BaseBox::ATOL_V4,
            new AtolV5Company($sno, $vat, $groupCode, $inn, $payment_address, $email),
            $payment_object,
            $payment_method,
            $testMode
        );

        $this->login = $login;
        $this->password = $password;
    }

    public static function fromArray($authArray)
    {
        return new AtolV5Auth(
            $authArray['sno'],
            $authArray['vat'],
            $authArray['group_code'],
            $authArray['inn'],
            $authArray['payment_address'],
            $authArray['payment_object'],
            $authArray['payment_method'],
            $authArray['login'],
            $authArray['password'],
            $authArray['email']
        );
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public static function getAuthParams()
    {
        return [
            'box' => [
                'type' => BaseBox::ATOL_V5,
                'name' => 'АТОЛ Онлайн ФФД 1.2',
            ],
            'login' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'Логин',
                'validation' => 'Введите логин АТОЛ',
            ],
            'password' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'Пароль',
                'validation' => 'Введите пароль АТОЛ',
            ],
            'group_code' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'Идентификатор группы ККТ',
                'validation' => 'Введите идентификатор группы ККТ',
            ],
            'inn' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'ИИН',
                'validation' => 'Введите ИИН компании',
            ],
            'email' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'Email',
                'validation' => 'Введите email компании',
            ],
            'payment_address' => [
                'type' => self::INPUT_TYPE_TEXT,
                'placeholder' => 'Место расчётов',
                'validation' => 'Введите адрес места расчётов',
            ],
            'sno' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Система налогообложения',
                'validation' => 'Выберите систему налогообложения компании',
                'options' => AtolonlineV5::getTaxationSystems(),
            ],
            'vat' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Налоговая ставка',
                'validation' => 'Выберите налоговую ставку компании',
                'options' => AtolonlineV5::getVats(),
            ],
            'payment_method' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Способ расчёта',
                'validation' => 'Выберите способ расчёта',
                'options' => AtolonlineV5::getPaymentMethods(),
            ],
            'payment_object' => [
                'type' => self::INPUT_TYPE_SELECT,
                'placeholder' => 'Предмет расчёта',
                'validation' => 'Выберите предмет расчёта',
                'options' => AtolonlineV5::getPaymentObjects(),
            ],
        ];
    }
}