<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Box;

abstract class BaseBox {
    /**
     * Нет подключения к кассе
     */
    const NONEBOX = 'nonebox';

    /**
     * Касса АТОЛ API v4
     */
    const ATOL_V4 = 'atolbox';

    /**
     * Касса АТОЛ API v5
     */
    const ATOL_V5 = 'atolv5box';

    /**
     * Касса OrangeData
     */
    const ORANGEDATA = 'orangedatabox';

    /**
     * Создание чека оплаты. Параметры запроса одинаковы для все интегрированных касс.
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
     * @param array $data
     * @return mixed|void
     */
    abstract public function saveBill($data);

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
     * @param array $data
     * @return mixed|void
     */
    abstract public function refundBill($data);

    /**
     * Возвращает объект расчёта для доставки данной кассы
     *
     * @return mixed
     */
    abstract protected function getDeliveryPaymentObject();
}