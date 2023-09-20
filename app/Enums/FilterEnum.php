<?php

namespace App\Enums;

enum FilterEnum: string
{
    case Equal = 'whereEqual';
    case Like = 'whereLike';
    case In = 'whereIn';
    case Between = 'whereBetween';
    case Date = 'whereDate';
    case Datetime = 'whereDatetime';
    case Localized = 'whereLocalized';
}
