<?php

namespace Uol\BoaCompra\Enum;

use Exception;

class Language extends BaseEnum
{
    const pt_BR = 'pt-BR';
    const pt_PT = 'pt-PT';
    const en_US = 'en-US';
    const es_ES = 'es-ES';
    const tr_TR = 'tr-TR';

    /**
     * @param $name
     * @return string
     */
    public static function getCode($name)
    {
        try {
            if (self::isValidName($name)) {
                $languages = self::getConstants();

                return $languages[$name];
            }
        } catch (Exception $e) {
        }

        return self::en_US;
    }
}
