<?php

namespace Uol\BoaCompra\Enum;

use ReflectionClass;

class DataAssign
{
    const PAYMENT_METHOD_TYPE = 'payment_method_type';
    const PAYMENT_METHOD_SUB_TYPE = 'payment_method_sub_type';
    const CC_CID = 'cc_cid';
    const CC_TYPE = 'cc_type';
    const CC_EXP_YEAR = 'cc_exp_year';
    const CC_EXP_MONTH = 'cc_exp_month';
    const CC_LAST_4 = 'cc_last_4';
    const CC_TOKEN = 'cc_token';
    const CC_INSTALLMENTS = 'cc_installments';
    const CC_SAVE = 'cc_save';
    const CC_CODE = 'cc_code';
    const USE_SAVED_CC = 'use_saved_cc';

    static function getAdditionalInformationList()
    {
        $oClass = new ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}
