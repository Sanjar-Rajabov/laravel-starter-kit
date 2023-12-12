<?php

namespace App\Models;

use App\Enums\RelationEnum;
use App\Models\Interfaces\HasRelationsInterface;
use App\Models\Interfaces\SafelySaveInterface;
use App\Traits\DateSerializer;
use App\Traits\HasRelation;
use App\Traits\SafelySave;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * @property int $id
 * @property string $login
 */
class User extends Authenticatable implements SafelySaveInterface, HasRelationsInterface
{
    use SafelySave, HasRelation, HasFactory, DateSerializer;

    protected $guarded = ['id'];

    protected $hidden = [
        'password'
    ];

    public function getFillableRelations(): array
    {
        return [
            'addresses' => RelationEnum::OneToMany
        ];
    }

    public function password(): Attribute
    {
        return Attribute::make(
            fn($value) => $value,
            fn($value) => Hash::make($value)
        );
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
