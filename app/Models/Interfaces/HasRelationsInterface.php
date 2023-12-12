<?php

namespace App\Models\Interfaces;

use Exception;
use Illuminate\Database\Eloquent\Model;

interface HasRelationsInterface
{
    /**
     * @throws Exception
     */
    public function safelySaveRelations(array $attributes): void;

    public function saveRelations(array $attributes): void;

    public function deleteRelations(array $attributes): void;

    public function saveRelation(string $relation, array $attributes): Model;

    public function createRelation(string $relation, array $attributes): Model;

    public function updateRelation(string $relation, array $attributes): Model|null;
}
