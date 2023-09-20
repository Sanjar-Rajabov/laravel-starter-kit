<?php

namespace App\Enums;

enum RelationEnum
{
    case OneToOne;
    case OneToMany;
    case ManyToMany;
}
