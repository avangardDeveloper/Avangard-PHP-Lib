<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

Namespace AtolV5\Services\Request;

use AtolV5\DataObjects\AtolV5ReceiptPosition;
use AtolV5\SdkException;

/**
 * Все парараметры обязательны для заполнения. В наборе email|phone требуется хотя бы одно значение
 */
class CreateDocumentRequest extends BaseServiceRequest
{

    /** @var string идентификатор группы ККТ */
    protected $groupCode;
    /** @var string тип операции */
    protected $operationType;
    /** @var string */
    protected $token;
    /** @var string */
    protected $paymentAddress;
    /** @var string */
    protected $customerName;
    /** @var string */
    protected $customerEmail;
    /** @var int */
    protected $customerPhone;
    /** @var int */
    protected $inn;
    /** @var int */
    protected $timestamp;
    /** @var int */
    protected $paymentType;
    /** @var AtolV5ReceiptPosition[] Позиции в чеке */
    protected $receiptPositions;
    /** @var string */
    protected $externalId;
    /** @var string */
    protected $sno;
    /** @var string */
    protected $callbackUrl = '';
    /** @var string */
    protected $itemsType;
    /** @var string */
    protected $companyEmail;

    const TIMESTAMP_FORMAT = 'd.m.Y H:i:s';

    const
        OPERATION_TYPE_SELL = 'sell', // Приход
        OPERATION_TYPE_SELL_REFUND = 'sell_refund', // Возврат прихода
        OPERATION_TYPE_BUY = 'buy', // Расход
        OPERATION_TYPE_BUY_REFUND = 'buy_refund', // Возврат расхода
        OPERATION_TYPE_SELL_CORRECTION = 'sell_correction', // Коррекция прихода
        OPERATION_TYPE_BUY_CORRECTION = 'buy_correction', // Коррекция расхода
        OPERATION_TYPE_SELL_REFUND_CORRECTION = 'sell_refund_correction', // Коррекция возврата прихода
        OPERATION_TYPE_BUY_REFUND_CORRECTION = 'buy_refund_correction'; // Коррекция возврата расхода

    const
        PAYMENT_TYPE_CASH = 0, // наличными
        PAYMENT_TYPE_ELECTRON = 1, // электронными
        PAYMENT_TYPE_PRE_PAID = 2, // предварительная оплата (аванс)
        PAYMENT_TYPE_CREDIT = 3, // последующая оплата (кредит)
        PAYMENT_TYPE_OTHER = 4,// иная форма оплаты (встречное предоставление)
        PAYMENT_TYPE_ADDITIONAL = 5; // расширенный типы оплаты. для каждого фискального типа оплаты можно указать расширенный тип оплаты

    const
        SNO_OSN = 'osn', // общая СН
        SNO_USN_INCOME = 'usn_income', // упрощенная СН (доходы)
        SNO_USN_INCOME_OUTCOME = 'usn_income_outcome', // упрощенная СН (доходы минус расходы)
        SNO_ENDV = 'envd', // единый налог на вмененный доход
        SNO_ESN = 'esn', // единый сельскохозяйственный налог
        SNO_PATENT = 'patent'; // патентная СН

    const
        ITEMS_TYPE_RECEIPT = 'receipt', // Наименование параметра для передачи товаров при операциях прихода, расхода и возвратов
        ITEMS_TYPE_CORRECTION = 'correction'; // Наименование параметра для передачи товаров при операциях коррекции прихода, расхода

    /**
     * @inheritdoc
     */
    public function getRequestUrl($test = false)
    {
        return parent::getBaseUrl($test) . $this->groupCode . '/' . $this->operationType . '?token=' . $this->token;
    }

    /**
     * Добавить email компании. Максимальная длина 64 символа
     * @param string $email
     * @return CreateDocumentRequest
     */
    public function addCompanyEmail($email)
    {
        $this->companyEmail = substr($email, 0, 64);
        return $this;
    }

    /**
     * Добавить адрес магазина для оплаты (сайт)
     * @param string $address
     * @return CreateDocumentRequest
     */
    public function addMerchantAddress($address)
    {
        $this->paymentAddress = $address;
        return $this;
    }

    /**
     * Установить email покупателя
     * @param string $email
     * @return CreateDocumentRequest
     */
    public function addCustomerEmail($email)
    {
        $this->customerEmail = $email;
        return $this;
    }

    /**
     * Установить телефон покупателя
     * @param int $phone
     * @return CreateDocumentRequest
     */
    public function addCustomerPhone($phone)
    {
        $customerPhone = str_replace(' ', '', $phone);

        $plusPosition = strpos($phone, '+');
        if ($plusPosition === false || $plusPosition != 0) {
            $customerPhone = '+'.$customerPhone;
        }

        $this->customerPhone = $customerPhone;
        return $this;
    }

    /**
     * Установить имя покупателя
     * @param int $name
     * @return CreateDocumentRequest
     */
    public function addCustomerName($name)
    {
        $this->customerName = $name;
        return $this;
    }

    /**
     * Установить inn
     * @param int $inn
     * @return CreateDocumentRequest
     */
    public function addInn($inn)
    {
        $this->inn = (string)$inn;
        return $this;
    }

    /**
     * Установить timestamp
     * @param int $timestamp
     * @return CreateDocumentRequest
     */
    public function addTimestamp($timestamp)
    {
        $this->timestamp = date(self::TIMESTAMP_FORMAT, $timestamp);
        return $this;
    }

    /**
     * Установить тип платежа. Из констант
     * @param int $paymentType
     * throws SdkException
     * @return CreateDocumentRequest
     */
    public function addPaymentType($paymentType)
    {
        if (!in_array($paymentType, $this->getPaymentTypes())) {
            throw new SdkException('Wrong payment type');
        }

        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * Добавить позицию в чек
     * @param AtolV5ReceiptPosition $position
     * @return CreateDocumentRequest
     */
    public function addReceiptPosition(AtolV5ReceiptPosition $position)
    {
        $this->receiptPositions[] = $position;
        return $this;
    }

    /**
     * Установить номер чека, если это коррекция
     * @param string $externalId
     * @return CreateDocumentRequest
     */
    public function addExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * Добавить SNO. Если у организации один тип - оно не обязательное. Из констант
     * @param string $sno
     * @return CreateDocumentRequest
     * @throws SdkException
     */
    public function addSno($sno)
    {
        if (!in_array($sno, $this->getSnoTypes())) {
            throw new SdkException('Wrong sno type');
        }

        $this->sno = $sno;
        return $this;
    }

    /**
     * @param string $token Токен из запроса получения токена
     * @return CreateDocumentRequest
     */
    public function __construct($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Добавить тип операции и определить наименование параметра для передачи товаров
     * @param string $operationType Тип операции. Из констант
     * @return CreateDocumentRequest
     * @throws SdkException
     */
    public function addOperationType($operationType)
    {
        if (!in_array($operationType, $this->getOperationTypes())) {
            throw new SdkException('Wrong operation type');
        }

        $this->operationType = $operationType;
        $this->itemsType = (stristr($this->operationType, 'correction') !== FALSE) ? self::ITEMS_TYPE_CORRECTION : self::ITEMS_TYPE_RECEIPT;
        return $this;
    }

    /**
     * Установить url для обратного запроса
     * @param string $url
     * @return CreateDocumentRequest
     */
    public function addCallbackUrl($url)
    {
        $this->callbackUrl = $url;
        return $this;
    }

    /**
     * Добавить код группы
     * @param string $groupCode Идентификатор группы ККТ
     * @return CreateDocumentRequest
     */
    public function addGroupCode($groupCode)
    {
        $this->groupCode = $groupCode;
        return $this;
    }

    public function getParameters()
    {
        $totalAmount = 0;
        $items = [];
        foreach ($this->receiptPositions as $receiptPosition) {
            $totalAmount += $receiptPosition->getPositionSum();
            $items[] = $receiptPosition->getParameters();
        }

        return [
            'timestamp' => $this->timestamp,
            'external_id' => $this->externalId,
            'receipt' => [
                'client' => [
                    'email' => $this->customerEmail,
                    'phone' => $this->customerPhone,
                    'name' => $this->customerName
                ],
                'company' => [
                    'email' => $this->companyEmail,
                    'sno' => $this->sno,
                    'inn' => $this->inn,
                    'payment_address' => $this->paymentAddress,
                ],
                'items' => $items,
                'total' => $totalAmount,
                'payments' => [
                    [
                        'sum' => $totalAmount,
                        'type' => $this->paymentType,
                    ],
                ]
            ]
        ];
    }

    protected function getOperationTypes()
    {
        return [
            self::OPERATION_TYPE_SELL, // Приход
            self::OPERATION_TYPE_SELL_REFUND, // Возврат прихода
            self::OPERATION_TYPE_BUY, // Расход
            self::OPERATION_TYPE_BUY_REFUND, // Возврат расхода
            self::OPERATION_TYPE_SELL_CORRECTION, // Коррекция прихода
            self::OPERATION_TYPE_BUY_CORRECTION, // Коррекция расхода
            self::OPERATION_TYPE_SELL_REFUND_CORRECTION, // Коррекция возврата прихода
            self::OPERATION_TYPE_BUY_REFUND_CORRECTION, // Коррекция возврата расхода
        ];
    }

    protected function getPaymentTypes()
    {
        return [
            self::PAYMENT_TYPE_CASH, // наличными
            self::PAYMENT_TYPE_ELECTRON, // электронными
            self::PAYMENT_TYPE_PRE_PAID, // предварительная оплата (аванс)
            self::PAYMENT_TYPE_CREDIT, // последующая оплата (кредит)
            self::PAYMENT_TYPE_OTHER, // иная форма оплаты (встречное предоставление)
            self::PAYMENT_TYPE_ADDITIONAL, // расширенный типы оплаты. для каждого фискального типа оплаты можно указать расширенный тип оплаты
        ];
    }

    protected function getSnoTypes()
    {
        return [
            self::SNO_OSN, // общая СН
            self::SNO_USN_INCOME, // упрощенная СН (доходы)
            self::SNO_USN_INCOME_OUTCOME, // упрощенная СН (доходы минус расходы)
            self::SNO_ENDV, // единый налог на вмененный доход
            self::SNO_ESN, // единый сельскохозяйственный налог
            self::SNO_PATENT, // патентная СН
        ];
    }
}
