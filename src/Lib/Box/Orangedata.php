<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Box;

use Box\DataObjects\ReceiptEntity;
use GuzzleHttp\Client as NetClient;
use OrangeDataClient\DataObjects\OrangeDataAuth;
use OrangeDataClient\OrangeDataClient;

/**
 * Class orangedata
 *
 * @package Avangard\boxFactory
 */
class Orangedata extends BaseBox implements GenerateBox
{
    /**
     * Object of orangedata library
     *
     * @var OrangeDataClient
     */
    protected $client;

    /**
     * orangedata constructor.
     *
     * @param OrangeDataAuth $auth
     * @param NetClient $netClient
     */
    public function __construct($auth, NetClient $netClient)
    {
        $this->client = new OrangeDataClient($auth, $netClient);

        $result = $this->client->check_connection();

        if ($result[1]['http_code'] != 200) {
            throw new \InvalidArgumentException(
                "OrangaData Auth error: " . $result[0], $result[1]['http_code']
            );
        }
    }

    /**
     * Prepare receipt data for sending
     *
     * @param ReceiptEntity $receiptEntity
     * @param $type
     * @throws \Exception
     */
    private function prepareReceipt($receiptEntity, $type)
    {
        $this->client->create_order([
            'id' => $receiptEntity->getId(),
            'type' => $type,
            'customerContact' => (!empty($receiptEntity->getClientEmail())
                ? $receiptEntity->getClientEmail() : $receiptEntity->getClientName()),
        ]);

        foreach ($receiptEntity->getItems() as $receiptItemEntity) {
            $item = [
                'quantity' => $receiptItemEntity->getQuantity(),
                'price' => round(($receiptItemEntity->getSum() / $receiptItemEntity->getQuantity()), 2),
                'tax' => $this->mathVat($this->client->getVat()),
                'text' => $receiptItemEntity->getName(),
                'paymentMethodType' => $this->client->getPaymentMethod(),
                'paymentSubjectType' => $receiptItemEntity->isDelivery() ? $this->getDeliveryPaymentObject() : $this->client->getPaymentObject(),
                'supplierInfo' => '',
                'supplierINN' => '',
                'agentType' => '',
                'agentInfo' => '',
                'additionalAttribute' => '',
                'manufacturerCountryCode' => '',
                'customsDeclarationNumber' => '',
                'excise' => 0
            ];
            $this->client->add_position_to_order($item);
        }

        $payment = [
            'type' => 2,
            'amount' => $receiptEntity->getTotal(),
        ];

        $this->client->add_payment_to_order($payment);
    }

    /**
     * Создание чека. Параметры запроса одинаковы для все интегрированных касс.
     *
     * - id уникальный идентификатор чека
     * - time время создания чека в строковом представлении
     * - client массив данных о клиенте:
     *      - name имя
     *      - email почта
     *      - phone телефон
     * Имя и (почта или телефон) обязательны к заполнению
     * - items массив объектов сведений о товарах:
     *      - name наименование товара
     *      - price цена товара
     *      - quantity количество товара
     *      - sum сумма по товару с учетом скидки
     *      - payment_method метод расчетов
     *      - payment_object объект расчетов
     *      - vat ставка налогооблажения
     * - total общая сумма платежа
     *
     * @param ReceiptEntity $data
     * @return array|mixed|void
     * @throws \Exception
     */
    public function saveBill($data)
    {
        $this->prepareReceipt($data, 1);

        $result = $this->client->send_order();

        return $result;
    }

    /**
     * Создание чека возврата. Параметры запроса одинаковы для все интегрированных касс.
     *
     * - id уникальный идентификатор чека
     * - time время создания чека в строковом представлении
     * - client массив данных о клиенте:
     *      - name имя
     *      - email почта
     *      - phone телефон
     * Имя и (почта или телефон) обязательны к заполнению
     * - items массив объектов сведений о товарах:
     *      - name наименование товара
     *      - price цена товара
     *      - quantity количество товара
     *      - sum сумма по товару с учетом скидки
     *      - payment_method метод расчетов
     *      - payment_object объект расчетов
     *      - vat ставка налогооблажения
     * - total общая сумма платежа
     *
     * @param ReceiptEntity $data
     * @return mixed|void
     * @throws \Exception
     */
    public function refundBill($data)
    {
        $this->prepareReceipt($data, 2);

        $result = $this->client->send_order();

        return $result;
    }

    /**
     * Calculate vat
     *
     * @param $type
     * @return int
     */
    private function mathVat($type)
    {
        switch ($type) {
            case "none":
                return 6;
            case "vat0":
                return 5;
            case "vat10":
                return 2;
            case "vat110":
                return 4;
            case "vat20":
                return 1;
            case "vat120":
                return 3;
            default:
                throw new \InvalidArgumentException(
                    "Incorrect vat"
                );
        }
    }

    protected function getDeliveryPaymentObject()
    {
        return $this->client->getDeliveryPaymentObject();
    }

    /**
     * Get boxes payment methods
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            1 => 'Предоплата 100%',
            2 => 'Частичная предоплата',
            3 => 'Аванс',
            4 => 'Полный расчет',
            5 => 'Частичный расчет и кредит',
            6 => 'Передача в кредит',
            7 => 'Оплата кредита'
        ];
    }

    /**
     * Get boxes payment objects
     *
     * @return array
     */
    public static function getPaymentObjects()
    {
        return [
            1 => 'Товар',
            2 => 'Подакцизный товар',
            3 => 'Работа',
            4 => 'Услуга',
            5 => 'Ставка азартной игры',
            6 => 'Выигрыш азартной игры',
            7 => 'Лотерейный билет',
            8 => 'Выигрыш лотереи',
            9 => 'Предоставление РИД',
            10 => 'Платеж',
            11 => 'Агентское вознаграждение',
            12 => 'Выплата',
            13 => 'Иной предмет расчета',
            14 => 'Имущественное право',
            15 => 'Внереализационный доход*',
            16 => 'Страховые взносы*',
            17 => 'Торговый сбор',
            18 => 'Курортный сбор',
            19 => 'Залог',
            20 => 'Расход',
            21 => 'Взносы на обязательное пенсионное страхование ИП',
            22 => 'Взносы на обязательное пенсионное страхование',
            23 => 'Взносы на обязательное медицинское страхование ИП',
            24 => 'Взносы на обязательное медицинское страхование',
            25 => 'Взносы на обязательное социальное страхование',
            26 => 'Платеж казино',
            27 => 'Выдача денежных средств',
            30 => 'АТНМ (не имеющем кода маркировки)',
            31 => 'АТМ (имеющем код маркировки)',
            32 => 'ТНМ',
            33 => 'ТМ',
        ];
    }

    /**
     * Get boxes taxation systems
     *
     * @return array
     */
    public static function getTaxationSystems()
    {
        return [
            0 => 'Общая, ОСН',
            1 => 'Упрощенная доход, УСН доход',
            2 => 'Упрощенная доход минус расход, УСН доход - расход',
            3 => 'Единый налог на вмененный доход, ЕНВД',
            4 => 'Единый сельскохозяйственный налог, ЕСН',
            5 => 'Патентная система налогообложения, Патент'
        ];
    }

    /**
     * Get boxes vats
     *
     * @return array
     */
    public static function getVats()
    {
        return [
            'none' => 'Без НДС',
            'vat0' => 'НДС 0%',
            'vat10' => 'НДС 10%',
            'vat110' => 'Рассчетный НДС 10%',
            'vat20' => 'НДС 20%',
            'vat120' => 'Рассчетный НДС 20%'
        ];
    }

    /**
     * Получить доступные версии ФФД
     *
     * @return array
     */
    public static function getFfdVersions()
    {
        return [
            2 => 'ФФД 1.05',
            4 => 'ФФД 1.2'
        ];
    }
}