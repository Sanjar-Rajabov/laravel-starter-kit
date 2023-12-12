<?php

namespace App\Enums;

enum SearchTypes: string
{
    case Int = 'whereInt';
    case Boolean = 'whereBoolean';
    case String = 'whereString';
    case Localized = 'whereLocalized';
}
