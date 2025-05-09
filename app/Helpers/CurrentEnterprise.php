<?php

namespace App\Helpers;

class CurrentEnterprise
{
    protected static $enterpriseId;

    public static function set($enterpriseId)
    {
        self::$enterpriseId = $enterpriseId;
    }

    public static function get()
    {
        return self::$enterpriseId;
    }
}
