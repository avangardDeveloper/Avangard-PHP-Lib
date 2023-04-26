<?php

namespace Avangard\Lib\Box;

use AtolV4\DataObjects\AtolV4Auth;
use AtolV5\DataObjects\AtolV5Auth;
use Box\BaseBox;
use OrangeDataClient\DataObjects\OrangeDataAuth;

abstract class BoxAuthFactory
{
    /**
     * Фабрика, которая создаёт объекты-наследники класса BaseAuth из переданного JSON
     *
     * @param $boxJson
     * @return AtolV4Auth|AtolV5Auth|OrangeDataAuth|null
     */
    public static function createBoxAuth($boxJson)
    {
        try {
            $boxArray = json_decode($boxJson);

            if (empty($boxJson['boxType'])) {
                return null;
            }

            switch ($boxJson['boxType']) {
                case BaseBox::ATOL_V4:
                    return AtolV4Auth::fromArray($boxArray);
                case BaseBox::ATOL_V5:
                    return AtolV5Auth::fromArray($boxArray);
                case BaseBox::ORANGEDATA:
                    return OrangeDataAuth::fromArray($boxArray);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }
}