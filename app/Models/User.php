<?php

namespace App\Models;

use App\Enums\RelationEnum;
use App\Traits\HasRelation;
use App\Traits\SafelySave;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * @property int $id
 * @property string $login
 */
class User extends Authenticatable
{
    use SafelySave, HasRelation;

    protected $fillable = [
        'login' => 'string',
        'password' => 'string'
    ];

    protected $hidden = [
        'password', 'created_at', 'updated_at'
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
