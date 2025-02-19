<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class InvoiceHelper
{
    public static function generatUniqueNUmber():string
    {
        $time=now()->format('YmdHis');
        $random=Str::upper(Str::random(6));
        return "inv-{$time}-{$random}";
    }
}