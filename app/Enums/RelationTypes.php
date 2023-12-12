<?php

namespace App\Enums;

enum RelationTypes
{
    case OneToOne;
    case OneToMany;
    case ManyToMany;
}
