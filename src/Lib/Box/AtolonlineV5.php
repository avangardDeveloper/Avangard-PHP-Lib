<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Box;

use AtolV5\DataObjects\AtolV5Auth;
use AtolV5\DataObjects\AtolV5Company;
use AtolV5\DataObjects\AtolV5ReceiptPosition;
use AtolV5\SdkException;
use AtolV5\Services\Request\BaseServiceRequest;
use AtolV5\Services\Request\CreateDocumentRequest;
use AtolV5\Services\Request\GetTokenRequest;
use AtolV5\Services\Response\CreateDocumentResponse;
use AtolV5\Services\Response\GetTokenResponse;
use Box\DataObjects\ReceiptEntity;
use GuzzleHttp\Client as NetClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Atolonline
 *
 * @package Avangard\boxFactory
 */
class AtolonlineV5 extends BaseBox implements GenerateBox
{
    /**
     * Object of Guzzle client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;
    /**
     * Object of atol library
     *
     * @var string
     */
    protected $token;
    /**
     * Object of our company
     *
     * @var AtolV5Company
     */
    protected $company;

    /**
     * Предмет расчёта
     *
     * @var string
     */
    protected $payment_object;

    /**
     * Способ расчёта
     *
     * @var string
     */
    protected $payment_method;

    /**
     * Test mode on/off
     *
     * @var bool
     */
    protected $test = false;

    /**
     * atolonlinev5 constructor.
     *
     * @param AtolV5Auth $auth
     * @param NetClient $client
     * @throws GuzzleException
     */
    public function __construct(AtolV5Auth $auth, NetClient $client)
    {
        $this->client = $client;

        $tokenService = new GetTokenRequest($auth->getLogin(), $auth->getPassword());
        $response = $this->sendRequest($tokenService, 'GET');
        $tokenResponse = new GetTokenResponse($response);

        if (!$tokenResponse->isValid()) {
            throw new \InvalidArgumentException(
                "AtolOnline response: " . $tokenResponse->getErrorDescription(), $tokenResponse->getErrorCode()
            );
        }

        $this->token = $tokenResponse;
        $this->company = $auth->getCompany();
        $this->test = $auth->isTestMode();
        $this->payment_object = $auth->getPaymentObject();
        $this->payment_method = $auth->getPaymentMethod();
    }

    /**
     * Send request to atol
     *
     * @param BaseServiceRequest $service
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendRequest(BaseServiceRequest $service, $method = 'POST') {
        $requestParameters = $service->getParameters();
        $requestUrl = $service->getRequestUrl($this->test);

        if($method == "POST") {
            $result = $this->client->request('POST', $requestUrl, ['json' => $requestParameters]);
        } else {
            $result = $this->client->request('GET', $requestUrl);
        }

        $status = $result->getStatusCode();

        if (!in_array($status, [200, 400, 401])) {
            throw new \InvalidArgumentException(
                "Atol error. Incorrect http response code: " . $status, $status
            );
        }

        $response = $result->getBody()->getContents();

        $decodedResponse = json_decode($response);
        if(empty($decodedResponse)) {
            throw new \InvalidArgumentException(
                "Atol error. Empty response or not json response"
            );
        }

        return $decodedResponse;
    }

    /**
     * Prepare receipt data for sending
     *
     * @param ReceiptEntity $data
     * @param string $type
     * @return CreateDocumentRequest
     * @throws SdkException
     */
    private function prepareReceipt($receiptEntity, $type)
    {
        $createDocumentService = (new CreateDocumentRequest($this->token));

        $createDocumentService->addCustomerName($receiptEntity->getClientName());

        $clientEmail = $receiptEntity->getClientEmail();
        if (!empty($clientEmail)) {
            $createDocumentService->addCustomerEmail($clientEmail);
        }

        $clientPhone = $receiptEntity->getClientPhone();
        if (!empty($clientPhone)) {
            $createDocumentService->addCustomerPhone($clientPhone);
        }

        $createDocumentService
            ->addGroupCode($this->company->getGroupCode())
            ->addCompanyEmail($this->company->getEmail())
            ->addSno($this->company->getSno())
            ->addInn($this->company->getInn())
            ->addMerchantAddress($this->company->getPaymentAddress())
            ->addOperationType($type)
            ->addPaymentType(CreateDocumentRequest::PAYMENT_TYPE_ELECTRON)
            ->addExternalId($receiptEntity->getId())
            ->addTimestamp($receiptEntity->getTime());

        foreach ($receiptEntity->getItems() as $receiptItemEntity) {
            $receiptPosition = new AtolV5ReceiptPosition(
                $receiptItemEntity->getName(),
                $receiptItemEntity->getPrice(),
                $receiptItemEntity->getQuantity(),
                $receiptItemEntity->getMeasure(),
                $this->company->getVat(),
                $receiptItemEntity->getSum(),
                $this->payment_method,
                !empty($receiptItemEntity->getPaymentObject())
                    ? $receiptItemEntity->getPaymentObject()
                    : $this->payment_object
            );

            $createDocumentService->addReceiptPosition($receiptPosition);
        }

        return $createDocumentService;
    }

    /**
     * Создание чека. Параметры запроса одинаковы для все интегрированных касс.
     *
     * - id уникальный идентификатор чека
     * - time время создания чека в виде timestamp
     * - client массив данных о клиенте:
     *      - name имя
     *      - email почта
     *      - phone телефон
     * Имя и (почта или телефон) обязательны к заполнению
     * - items массив объектов сведений о товарах:
     *      - name наименование товара
     *      - price цена товара
     *      - quantity количество товара
     *      - measure единицы измерения товара
     *      - sum сумма по товару с учетом скидки
     *      - payment_method метод расчетов
     *      - payment_object объект расчетов
     *      - vat ставка налогооблажения
     *
     * @param array $data
     * @return array
     * @throws SdkException
     * @throws GuzzleException
     */
    public function saveBill($data)
    {
        $request = $this->prepareReceipt($data, CreateDocumentRequest::OPERATION_TYPE_SELL);

        $createDocumentResponse = new CreateDocumentResponse($this->sendRequest($request));

        if (!$createDocumentResponse->isValid()) {
            throw new \InvalidArgumentException(
                "AtolOnline response: " . $createDocumentResponse->getErrorDescription(), $createDocumentResponse->getErrorCode()
            );
        }

        return ['status' => $createDocumentResponse->status, 'uuid' => $createDocumentResponse->uuid];
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
     * @param array $data
     * @return array
     * @throws GuzzleException
     * @throws SdkException
     */
    public function refundBill($data)
    {
        $request = $this->prepareReceipt($data, CreateDocumentRequest::OPERATION_TYPE_SELL_REFUND);

        $createDocumentResponse = new CreateDocumentResponse($this->sendRequest($request));

        if (!$createDocumentResponse->isValid()) {
            throw new \InvalidArgumentException(
                "AtolOnline response: " . $createDocumentResponse->getErrorDescription(), $createDocumentResponse->getErrorCode()
            );
        }

        return ['status' => $createDocumentResponse->status, 'uuid' => $createDocumentResponse->uuid];
    }

    /**
     * Get boxes payment methods
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            'full_prepayment' => 'Предоплата 100%. Полная предварительная оплата до момента передачи предмета расчета',
            'prepayment' => 'Предоплата. Частичная предварительная оплата до момента передачи предмета расчета',
            'advance' => 'Аванс',
            'full_payment' => 'Полный расчет. Полная оплата, в том числе с учетом аванса     (предварительной оплаты) в момент передачи предмета расчета',
            'partial_payment' => 'Частичный расчет и кредит. Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит',
            'credit' => 'Передача в кредит. Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит',
            'credit_payment' => 'Оплата кредита. Оплата предмета расчета после его передачи с оплатой в кредит (оплата кредита)'
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
            1 => 'О реализуемом товаре, за исключением подакцизного товара и товара, подлежащего маркировке средствами идентификации (наименование и иные сведения, описывающие товар)',
            2 => 'О реализуемом подакцизном товаре, за исключением товара, подлежащего маркировке средствами идентификации (наименование и иные сведения, описывающие товар)',
            3 => 'О выполняемой работе (наименование и иные сведения, описывающие работу)',
            4 => 'Об оказываемой услуге (наименование и иные сведения, описывающие услугу)',
            5 => 'О приеме ставок при осуществлении деятельности по проведению азартных игр',
            6 => 'О выплате денежных средств в виде выигрыша при осуществлении деятельности по проведению азартных игр',
            7 => 'О приеме денежных средств при реализации лотерейных билетов, электронных лотерейных билетов, приеме лотерейных ставок при осуществлении деятельности по проведению лотерей',
            8 => 'О выплате денежных средств в виде выигрыша при осуществлении деятельности по проведению лотерей',
            9 => 'О предоставлении прав на использование результатов интеллектуальной деятельности или средств индивидуализации',
            10 => 'Об авансе, задатке, предоплате, кредите',
            11 => 'О вознаграждении пользователя, являющегося платежным агентом (субагентом), банковским платежным агентом (субагентом), комиссионером, поверенным или иным агентом',
            12 => 'О взносе в счет оплаты, пени, штрафе, вознаграждении, бонусе и ином аналогичном предмете расчета',
            13 => 'О предмете расчета, не относящемуся к предметам расчета, которым может быть присвоено значение от «1» до «11» и от «14» до «26»',
            14 => 'О передаче имущественных прав',
            15 => 'О внереализационном доходе',
            16 => 'О суммах расходов, платежей и взносов, указанных в подпунктах 2 и 3 пункта Налогового кодекса Российской Федерации, уменьшающих сумму налога',
            17 => 'О суммах уплаченного торгового сбора',
            18 => 'О курортном сборе',
            19 => 'О залоге',
            20 => 'О суммах произведенных расходов в соответствии со статьей 346.16 Налогового кодекса Российской Федерации, уменьшающих доход',
            21 => 'О страховых взносах на обязательное пенсионное страхование, уплачиваемых ИП, не производящими выплаты и иные вознаграждения физическим лицам',
            22 => 'О страховых взносах на обязательное пенсионное страхование, уплачиваемых организациями и ИП, производящими выплаты и иные вознаграждения физическим лицам',
            23 => 'О страховых взносах на обязательное медицинское страхование, уплачиваемых ИП, не производящими выплаты и иные вознаграждения физическим лицам',
            24 => 'О страховых взносах на обязательное медицинское страхование, уплачиваемые организациями и ИП, производящими выплаты и иные вознаграждения физическим лицам',
            25 => 'О страховых взносах на обязательное социальное страхование на случай временной нетрудоспособности и в связи с материнством, на обязательное социальное страхование от несчастных случаев на производстве и профессиональных заболеваний',
            26 => 'О приеме и выплате денежных средств при осуществлении казино и залами игровых автоматов расчетов с использованием обменных знаков игорного заведения',
            27 => 'О выдаче денежных средств банковским платежным агентом',
            30 => 'О реализуемом подакцизном товаре, подлежащем маркировке средством идентификации, не имеющем кода маркировки',
            31 => 'О реализуемом подакцизном товаре, подлежащем маркировке средством идентификации, имеющем код маркировки',
            32 => 'О реализуемом товаре, подлежащем маркировке средством идентификации, не имеющем кода маркировки, за исключением подакцизного товара',
            33 => 'О реализуемом товаре, подлежащем маркировке средством идентификации, имеющем код маркировки, за исключением подакцизного товара',
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
            'osn' => 'Общая СН',
            'usn_income' => 'Упрощенная СН (доходы)',
            'usn_income_outcome' => 'Упрощенная СН (доходы минус расходы)',
            'envd' => 'Единый налог на вмененный доход',
            'esn' => 'Единый сельскохозяйственный налог',
            'patent' => 'Патентная СН'
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
}