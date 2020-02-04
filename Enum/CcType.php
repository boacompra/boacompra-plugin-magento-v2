<?php

namespace Uol\BoaCompra\Enum;

use ReflectionException;

class CcType extends BaseEnum
{
    const AE = 'amex';
    const VI = 'visa';
    const MC = 'mastercard';
    const DI = 'discover';
    const SM = 'maestro';
    const SO = 'solo';
    const JC = 'jcb';
    const DN = 'diners';
    const HC = 'hipercard';
    const EL = 'elo';
    const AU = 'aura';

    /**
     * @param $name
     * @return string|void
     */
    public static function getCode($name)
    {
        try {
            $types = self::getConstants();
            return array_search($name, $types);
        } catch (ReflectionException $e) {
        }
    }
}
