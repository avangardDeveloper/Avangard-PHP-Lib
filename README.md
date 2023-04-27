# AVANGARD PHP Client
AVANGARD API v4 client for PHP

## Installation

Usage is as simple as 

1. Create file <b>composer.json</b> in any directory

2. Add in this file:
```json
{
    "require": {
        "avangard/api": "dev-master"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/avangardDeveloper/Avangard-PHP-Lib"
        }
    ]
}
```

2. Run in this directory
```php
composer install
```

## How to use it

### Parametrs

Constructor's params are:
- shop id *
- shop password *
- shop signature *
- bank's server signature *
- box type:
    - ApiClient::NONEBOX (default)
    - ApiClient::ATOLBOX
    - ApiClient::ORANGEDATABOX
- box auth (default array()):
    - for ATOL:
        - login (login in atol)
        - pass (passworn in atol)
        - company:
            - group (box group in atol)
            - sno (taxation of company - from method getTaxationSystem())
            - inn (company's inn)
            - payment_address (company's payment address)
        - testMode (send test data true/false, default false)
    - for Orange Data:
        - inn (company's inn)
        - sno (taxation of company - from method getTaxationSystem())
        - api_url (access url)
        - sign_pkey (path to private_key.pem)
        - ssl_client_key (path to client.key)
        - ssl_client_crt (path to client.crt)
        - ssl_ca_cert (path to cacert.pem)
        - ssl_client_crt_pass (password to client.crt)
- proxy (proxy's http url, default null)

### Add in project

To add library in php code, include autoload script:
```php
require_once ("vendor/autoload.php");
use Avangard\ApiClient;
```
**ATTENTION!**
All methods of this library need to use with try/catch construction's:
```php
try {
    //All method's here...
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
        //Your custom lod's here...
    }
}
```
Static method \Avangard\Lib\Logger::log recommended to use in case of $debug param, which set in admin-panel. This method send error's log into developer's telegram. 

!!! All fields with AMOUNT should contain cent's !!!

### Method's

1.  prepareForms - prepare payment form.

Params:
- order:
    - AMOUNT (number, require) сумма к оплате
    - ORDER_NUMBER (string, require) номер заказа в магазине
    - ORDER_DESCRIPTION (string, require) описание заказа в магазине
    - LANGUAGE (string, require, default 'RU') описание заказа в магазине
    - BACK_URL (string, require) ссылка безусловного редиректа
    - BACK_URL_OK (string) ссылка успешного редиректа
    - BACK_URL_FAIL (string) ссылка НЕуспешного редиректа
    - CLIENT_NAME (string) имя плательщика
    - CLIENT_ADDRESS (string) физический адрес плательщика
    - CLIENT_EMAIL (string) email плательщика
    - CLIENT_PHONE (string) телефон плательщика
    - CLIENT_IP (string) ip-адрес плательщика
- type:
    - ApiClient::HOST2HOST (Регистрирует заказ в интернет-эквайринге и возвращает TICKET-параметр для последующей оплаты заказа)
    - ApiClient::POSTFORM (Подготавливает параметры для HTML формы оплаты, показываемой на стороне клиента - часто требуется для CMS)
    - ApiClient::GETURL (Регистрирует заказ в интернет-эквайринге и возвращает ссылку для последующей оплаты заказа)
    
Response:
- type ApiClient::HOST2HOST:
```
array {
  ["URL"] => string "https://pay.avangard.ru/iacq/pay"
  ["METHOD"] => string "get"
  ["INPUTS"] => array {
    ["TICKET"] => string "JGceLCtt000012682687LskJXuIpbfmpgeeKgkcj"
  }
}
```
- type ApiClient::POSTFORM:
```
array {
  ["URL"] => string "https://pay.avangard.ru/iacq/post"
  ["METHOD"] => string "post"
  ["INPUTS"] => array {
    ["SHOP_ID"] => string "1"
    ["SHOP_PASSWD"] => string "pass"
    ["AMOUNT"] => int 2
    ["ORDER_NUMBER"] => string "sa12"
    ["ORDER_DESCRIPTION"] => string "lalala"
    ["BACK_URL"] => string "http://example.ru/payments.php/avangard/?result=success"
    ["LANGUAGE"] => string "RU"
    ["SIGNATURE"] => string "1EBE4761D9B165D8FF784803686AF511"
  }
}
```
- type ApiClient::GETURL:
```
string "https://pay.avangard.ru/iacq/pay?ticket=JGceLCtt000012682687LskJXuIpbfmpgeeKgkcj"
```
    
Example HOST2HOST/GETURL:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );
    
    $mass = [
        'AMOUNT' => 2,
        'ORDER_NUMBER' => 'sa12',
        'ORDER_DESCRIPTION' => 'lalala',
        'BACK_URL' => 'http://example.ru/payments.php/avangard/?result=success'
    ];
    $rez = $new->request->prepareForms($mass, ApiClient::HOST2HOST);
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

Example POSTFORM:
```php
<?php
require_once "vendor/autoload.php";

use Avangard\ApiClient;

$new = new ApiClient(
    1,
    'pass',
    'sign1',
    'sign2'
);

$mass = [
    'AMOUNT' => 15,
    'ORDER_NUMBER' => 'sa123',
    'ORDER_DESCRIPTION' => 'lalala',
    'BACK_URL' => 'http://example.ru/payments.php/avangard/?result=success'
];
$rez = $new->request->prepareForms($mass, ApiClient::POSTFORM);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta http-equiv="Cache-Control" content="no-cache"/>
    <meta http-equiv="expires" content="Mon, 26 Jul 1997 05:00:00 GMT"/>
    <title>Авангард Интернет Эквайринг </title>
</head>
<body>
<div style="width: 30%; margin: 50px auto 50px;">
    <?php
    echo '<form id="FORM" action="' . $rez['URL'] . '" method="' . $rez['METHOD'] . '">';
    ?>
        <?php
        foreach ($rez['INPUTS'] as $key => $value) {
            echo '<input name="' . $key . '" value="' . $value . '" type="hidden">';
        }
        ?>
        <div style="text-align: center; margin-top: 30px;">
            <input type="submit" value="ОПЛАТИТЬ" class="btn"/>
        </div>
    </form>
</div>
</body>
</html>
```

2. orderRegister - also you can register order in the bank's system and get ticket.

Params:
- AMOUNT (number, require) сумма к оплате
- ORDER_NUMBER (string, require) номер заказа в магазине
- ORDER_DESCRIPTION (string, require) описание заказа в магазине
- LANGUAGE (string, require, default 'RU') описание заказа в магазине
- BACK_URL (string, require) ссылка безусловного редиректа
- BACK_URL_OK (string) ссылка успешного редиректа
- BACK_URL_FAIL (string) ссылка НЕуспешного редиректа
- CLIENT_NAME (string) имя плательщика
- CLIENT_ADDRESS (string) физический адрес плательщика
- CLIENT_EMAIL (string) email плательщика
- CLIENT_PHONE (string) телефон плательщика
- CLIENT_IP (string) ip-адрес плательщика

Response:
```
array {
  ["TICKET"] => string "xQElJQhi000012682701rKuBUpngKsIsUBKPBmfM"
}
```

Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );
    
    $mass = [
        'AMOUNT' => 2,
        'ORDER_NUMBER' => 'sa12',
        'ORDER_DESCRIPTION' => 'lalala',
        'BACK_URL' => 'http://example.ru/payments.php/avangard/?result=success'
    ];

    $rez = $new->request->orderRegister($mass);
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

3. getOrderByTicket - we can get an information about order by ticket.

Param's:
- ticket (string, require)

Response:
see bank docs

Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );

    $rez = $new->request->getOrderByTicket("UWyNLGVh000012669958czZpckkboKNDpUysDhlL");
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

4. isCorrectHash - when the bank's system send http request, we can verify request by sign.

Param's:
Data of bank http request.

Response:
boolean

Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

$_REQUEST = array (
    'id' => '12663423',
    'signature' => '07EB5673A9ECD4506C112B3EE3E3AF80',
    'method_name' => 'D3S',
    'shop_id' => '1',
    'ticket' => 'OWXZAkWg000012663423irlhpRKbAevpPsymgoDu',
    'status_code' => '3',
    'auth_code' => '',
    'amount' => '200',
    'card_num' => '546938******1152',
    'order_number' => 'shop#1#1',
    'status_desc' => 'Исполнен',
    'status_date' => '2019-11-05 10:17:17.0',
    'refund_amount' => '0',
    'exp_mm' => '09',
    'exp_yy' => '22'
);

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );
    
    $rez = $new->request->isCorrectHash($_REQUEST);
    var_dump($rez); //true or false
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

5. sendResponse - send http header as an answer to bank and then die.

Response:
die();

Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

$_REQUEST = array (
    'id' => '12663423',
    'signature' => '07EB5673A9ECD4506C112B3EE3E3AF80',
    'method_name' => 'D3S',
    'shop_id' => '1',
    'ticket' => 'OWXZAkWg000012663423irlhpRKbAevpPsymgoDu',
    'status_code' => '3',
    'auth_code' => '',
    'amount' => '200',
    'card_num' => '546938******1152',
    'order_number' => 'shop#1#1',
    'status_desc' => 'Исполнен',
    'status_date' => '2019-11-05 10:17:17.0',
    'refund_amount' => '0',
    'exp_mm' => '09',
    'exp_yy' => '22'
);

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );
    
    $new->request->sendResponse();
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

6. orderRefund - we can make part/full refund by ticket. 

Param's:
- ticket (string, require)
- amount (number) in cent!

Response:
```
array {
  ["transaction_id"] => int "124665"
}
```

Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new Avangard\ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );

    $rez = $new->request->orderRefund("UWyNLGVh000012669958czZpckkboKNDpUysDhlL", 10000);
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

7. orderCancel - we can cancel order by ticket.
 
Param's:
- ticket (string, require)

Response:
boolean
 
Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new Avangard\ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );

    $rez = $new->request->orderCancel("UWyNLGVh000012669958czZpckkboKNDpUysDhlL");
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
        \Avangard\Lib\Logger::log($e);
    }
}
```

8. getOpersByOrderNumber - we can get operations by order number.

Param's:
- order number (string, require)

Response:
see bank docs
 
Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new Avangard\ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );

    $rez = $new->request->getOpersByOrderNumber("shop#1#1");
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

9. getOpersByDate - we can get operations by date.

Param's:
 - date (string, require)
 
 Response:
 see bank docs
 
Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new Avangard\ApiClient(
        1,
        'pass',
        'sign1',
        'sign2'
    );

    $rez = $new->request->getOpersByDate("2019-11-06");
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

10. sendBill - send sale receipt into box.

Param's:
- id (string, require) уникальный идентификатор чека
- time (string, require) время создания чека в строковом представлении
- client (object, require) массив данных о клиенте:
    - name (string, require) имя
    - email (string, require if empty phone) почта
    - phone (string, require if empty email) телефон
- items (array of object's, require) массив объектов сведений о товарах:
    - name (string, require) наименование товара
    - price (number, require) цена товара
    - quantity (integer, require) количество товара
    - sum (number, require) сумма по товару с учетом скидки
    - payment_method (string from getPaymentMethod(), require) метод расчетов
    - payment_object (string from getPaymentObject(), require) объект расчетов
    - vat (string from getTaxationSystem(), require) ставка налогооблажения

Response:
see current box docs
 
Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2',
        ApiClient::ATOLBOX,
        [
            'login' => 'test',
            'pass' => 'test',
            'company' => [
                'group' => 'test',
                'sno' => 'osn',
                'inn' => '111111111',
                'payment_address' => 'Москва, Сретенка 9'
            ],
            'testMode' => false
        ]
    );

    $receipt = [
        'id' => '1',
        'time' => date("Y-m-d H:i:s"),
        'client' => [
            'name' => 'Artur',
            'phone' => '88005553535',
            'email' => 'test@mail.ru'
        ],
        'items' => [[
            'name' => 'Test item',
            'price' => 2,
            'quantity' => 1,
            'sum' => 2,
            'payment_method' => "full_prepayment",
            'payment_object' => "commodity",
            'vat' => 'vat120'
        ]],
        'total' => 2
    ];

    $rez = $new->request->sendBill($receipt);
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

11. refundBill - send refund receipt into box.

Param's:
- id (string, require) уникальный идентификатор чека
- time (string, require) время создания чека в строковом представлении
- client (object, require) массив данных о клиенте:
    - name (string, require) имя
    - email (string, require if empty phone) почта
    - phone (string, require if empty email) телефон
- items (array of object's, require) массив объектов сведений о товарах:
    - name (string, require) наименование товара
    - price (number, require) цена товара
    - quantity (integer, require) количество товара
    - sum (number, require) сумма по товару с учетом скидки
    - payment_method (string from getPaymentMethod(), require) метод расчетов
    - payment_object (string from getPaymentObject(), require) объект расчетов
    - vat (string from getTaxationSystem(), require) ставка налогооблажения
 
 Response:
 see current box docs
 
Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $new = new ApiClient(
        1,
        'pass',
        'sign1',
        'sign2',
        ApiClient::ATOLBOX,
        [
            'login' => 'test',
            'pass' => 'test',
            'company' => [
                'group' => 'test',
                'sno' => 'osn',
                'inn' => '111111111',
                'payment_address' => 'Москва, Сретенка 9'
            ],
            'testMode' => false
        ]
    );

    $receipt = [
        'id' => '1',
        'time' => date("Y-m-d H:i:s"),
        'client' => [
            'name' => 'Artur',
            'phone' => '88005553535',
            'email' => 'test@mail.ru'
        ],
        'items' => [[
            'name' => 'Test item',
            'price' => 2,
            'quantity' => 1,
            'sum' => 2,
            'payment_method' => "full_prepayment",
            'payment_object' => "commodity",
            'vat' => 'vat120'
        ]],
        'total' => 2
    ];

    $rez = $new->request->refundBill($receipt);
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

12. getPaymentMethod - get all payment method's for current box.

Response:
```
array {
    code of payment method => description
}
```
 
Example:

```php
<?php
require_once "vendor/autoload.php";

$debug = true;

try {
    $rez = \Avangard\BoxFactory\Atolonline::getPaymentMethods();
    $rez = \Avangard\BoxFactory\Orangedata::getPaymentMethods();
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

13. getPaymentObject - get all payment object's for current box.

Response:
```
array {
    code of payment object => description
}
```
 
Example:

```php
<?php
require_once "vendor/autoload.php";

$debug = true;

try {
    $rez = \Avangard\BoxFactory\Atolonline::getPaymentObjects();
    $rez = \Avangard\BoxFactory\Orangedata::getPaymentObjects();
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

14. getTaxationSystem - get all taxation system's for current box.

Response:
```
array {
    code of taxation system => description
}
```
 
Example:

```php
<?php
require_once "vendor/autoload.php";

$debug = true;

try {
    $rez = \Avangard\BoxFactory\Atolonline::getTaxationSystemы();
    $rez = \Avangard\BoxFactory\Orangedata::getTaxationSystemы();
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

15. getVats - get all vat's for current box.

Response:
```
array {
    code of vat => description
}
```
 
Example:
```php
<?php
require_once "vendor/autoload.php";
use Avangard\ApiClient;

$debug = true;

try {
    $rez = \Avangard\BoxFactory\Atolonline::getVats();
    $rez = \Avangard\BoxFactory\Orangedata::getVats();
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

16. getApiVersions - get current API version's.

Response:
```
array {
    string "v4.0"
}
```
 
Example:
```php
<?php
require_once "vendor/autoload.php";

$debug = true;

try {
    $rez = \Avangard\ApiClient::getApiVersions();
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```

17. getVersion - get current library version.

Response:
```
string "Library version 1.0.0. Avangard (c) 2019."
```
 
Example:
```php
<?php
require_once "vendor/autoload.php";

$debug = true;

try {
    $rez = \Avangard\ApiClient::getVersion();
    print_r($rez);
} catch (\Exception $e) {
    if ($debug) {
         \Avangard\Lib\Logger::log($e);
     }
}
```
