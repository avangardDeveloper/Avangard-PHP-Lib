<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

Namespace Atol102\DataObjects;

abstract class BaseDataObject
{
    /**
     * Получить параметры, сгенерированные командой
     * @return array
     */
    public function getParameters()
    {
        $filledvars = array();
        foreach (get_object_vars($this) as $name => $value) {
            if ($value) {
                $filledvars[$name] = $value;
            }
        }

        return $filledvars;
    }
}
