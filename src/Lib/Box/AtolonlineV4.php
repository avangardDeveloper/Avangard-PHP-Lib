<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Box;

use AtolV4\DataObjects\AtolV4Auth;
use AtolV4\DataObjects\AtolV4Company;
use AtolV4\DataObjects\AtolV4ReceiptPosition;
use AtolV4\SdkException;
use AtolV4\Services\BaseServiceRequest;
use AtolV4\Services\CreateDocumentRequest;
use AtolV4\Services\CreateDocumentResponse;
use AtolV4\Services\GetTokenRequest;
use AtolV4\Services\GetTokenResponse;
use Box\DataObjects\ReceiptEntity;
use GuzzleHttp\Client as NetClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Atolonline
 *
 * @package Avangard\boxFactory
 */
class AtolonlineV4 extends BaseBox implements GenerateBox
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
     * @var AtolV4Company
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
     * Atolonline constructor.
     *
     * @param AtolV4Auth $auth
     * @param NetClient $client
     * @throws GuzzleException
     */
    public function __construct(AtolV4Auth $auth, NetClient $client)
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
                "Atol error. Incorrect http code: " . $status, $status
            );
        }

        $response = $result->getBody()->getContents();

        $decodedResponse = json_decode($response);
        if(empty($decodedResponse)){
            throw new \InvalidArgumentException(
                "Atol error. Empty response or not json response"
            );
        }

        return $decodedResponse;
    }

    /**
     * Prepare receipt data for sending
     *
     * @param ReceiptEntity $receiptEntity
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
            ->addInn($this->company->getInn())
            ->addMerchantAddress($this->company->getPaymentAddress())
            ->addSno($this->company->getSno())
            ->addInn($this->company->getInn())
            ->addOperationType($type)
            ->addPaymentType(CreateDocumentRequest::PAYMENT_TYPE_ELECTRON)
            ->addExternalId($receiptEntity->getId())
            ->addTimestamp(strtotime($receiptEntity->getTime()));

        foreach ($receiptEntity->getItems() as $receiptItemEntity) {
            $receiptPosition = new AtolV4ReceiptPosition(
                $receiptItemEntity->getName(),
                $receiptItemEntity->getPrice(),
                $receiptItemEntity->getQuantity(),
                $this->company->getVat(),
                $receiptItemEntity->getSum(),
                $this->payment_method,
                $receiptItemEntity->isDelivery() ? $this->getDeliveryPaymentObject() : $this->payment_object
            );

            $createDocumentService->addReceiptPosition($receiptPosition);
        }

        return $createDocumentService;
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
     *
     * @param ReceiptEntity $data
     * @return array
     * @throws GuzzleException
     * @throws SdkException
     */
    public function saveBill($data)
    {
        $data->modifyIdForSale();

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
     * @param ReceiptEntity $data
     * @return array
     * @throws GuzzleException
     * @throws SdkException
     */
    public function refundBill($data)
    {
        $data->modifyIdForRefund();

        $request = $this->prepareReceipt($data, CreateDocumentRequest::OPERATION_TYPE_SELL_REFUND);

        $createDocumentResponse = new CreateDocumentResponse($this->sendRequest($request));

        if (!$createDocumentResponse->isValid()) {
            throw new \InvalidArgumentException(
                "AtolOnline response: " . $createDocumentResponse->getErrorDescription(), $createDocumentResponse->getErrorCode()
            );
        }

        return ['status' => $createDocumentResponse->status, 'uuid' => $createDocumentResponse->uuid];
    }

    protected function getDeliveryPaymentObject()
    {
        return 'service';
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
            'commodity' => 'Товар. О реализуемом товаре, за исключением подакцизного товара (наименование и иные сведения, описывающие товар)',
            'excise' => 'Подакцизный товар. О реализуемом подакцизном товаре (наименование и иные сведения, описывающие товар)',
            'job' => 'Работа. О выполняемой работе (наименование и иные сведения, описывающие работу)',
            'service' => 'Услуга. Об оказываемой услуге (наименование и иные сведения, описывающие услугу)',
            'gambling_bet' => 'Ставка азартной игры. О приеме ставок при осуществлении деятельности по проведению азартных игр',
            'gambling_prize' => 'Выигрыш азартной игры. О выплате денежных средств в виде выигрыша при осуществлении деятельности по проведению азартных игр',
            'lottery' => 'Лотерейный билет. О приеме денежных средств при реализации лотерейных билетов, электронных лотерейных билетов, приеме лотерейных ставок при осуществлении деятельности по проведению лотерей',
            'lottery_prize' => 'Выигрыш лотереи. О выплате денежных средств в виде выигрыша при осуществлении деятельности по проведению лотерей',
            'intellectual_activity' => 'Предоставление результатов интеллектуальной деятельности. О предоставлении прав на использование результатов интеллектуальной деятельности или средств индивидуализации',
            'payment' => 'Платеж. Об авансе, задатке, предоплате, кредите, взносе в счет оплаты, пени, штрафе, вознаграждении, бонусе и ином аналогичном предмете расчета',
            'agent_commission' => 'Агентское вознаграждение. О вознаграждении пользователя, являющегося платежным агентом (субагентом), банковским платежным агентом (субагентом), комиссионером, поверенным или иным агентом',
            'award' => 'О взносе в счет оплаты пени, штрафе, вознаграждении, бонусе и ином аналогичном предмете расчета',
            'another' => 'Иной предмет расчета. О предмете расчета, не относящемуся к выше перечисленным предметам расчета',
            'property_right' => 'Имущественное право. О передаче имущественных прав',
            'non-operating_gain' => 'Внереализационный доход. О внереализационном доходе',
            'insurance_premium' => 'Страховые взносы. О суммах расходов, уменьшающих сумму налога (авансовых платежей) в соответствии с пунктом 3.1 статьи 346.21 Налогового кодекса Российской Федерации',
            'sales_tax' => 'Торговый сбор. О суммах уплаченного торгового сбора',
            'deposit' => 'Залог. О залоге',
            'expense' => 'Расход. О суммах произведенных расходов в соответствии со статьей 346.16 Налогового кодекса Российской Федерации, уменьшающих доход',
            'pension_insurance_ip' => 'Взносы на ОПС ИП. О страховых взносах на обязательное пенсионное страхование, уплачиваемых ИП, не производящими выплаты и иные вознаграждения физическим лицам',
            'pension_insurance' => 'Взносы на ОПС. О страховых взносах на обязательное пенсионное страхование, уплачиваемых организациями и ИП, производящими выплаты и иные вознаграждения физическим лицам',
            'medical_insurance_ip' => 'Взносы на ОМС ИП. О страховых взносах на обязательное медицинское страхование, уплачиваемых ИП, не производящими выплаты и иные вознаграждения физическим лицам',
            'medical_insurance' => 'Взносы на ОМС. О страховых взносах на обязательное медицинское страхование, уплачиваемые организациями и ИП, производящими выплаты и иные вознаграждения физическим лицам',
            'social_insurance' => 'Взносы на ОСС. О страховых взносах на обязательное социальное страхование на случай временной нетрудоспособности и в связи с материнством, на обязательное социальное страхование от несчастных случаев на производстве и профессиональных заболеваний',
            'casino_payment' => 'Платеж казино. О приеме и выплате денежных средств при осуществлении деятельности казино с использованием обменных знаков казино, в зале игровых автоматов'
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
            'osn' => 'общая СН',
            'usn_income' => 'упрощенная СН (доходы)',
            'usn_income_outcome' => 'упрощенная СН (доходы минус расходы)',
            'envd' => 'единый налог на вмененный доход',
            'esn' => 'единый сельскохозяйственный налог',
            'patent' => 'патентная СН'
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