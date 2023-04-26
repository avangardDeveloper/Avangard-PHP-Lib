<?php
namespace GenerateBox;

use AtolV4\DataObjects\AtolV4Auth;
use AtolV5\DataObjects\AtolV5Auth;
use OrangeDataClient\DataObjects\OrangeDataAuth;

class GenerateBoxController
{
    /**
     * Классы для авторизации всех реализованных касс. Каждый должен реализовывать интерфейс GenerateBoxAuth
     */
    const BOX_AUTH_CLASSES = [
        AtolV4Auth::class,
        AtolV5Auth::class,
        OrangeDataAuth::class,
    ];

    public static function getBoxes()
    {
        $boxes = [];

        foreach (self::BOX_AUTH_CLASSES as $boxAuthClass) {
            $boxes[] = call_user_func($boxAuthClass.'::getAuthParams');
        }

        return $boxes;
    }
}