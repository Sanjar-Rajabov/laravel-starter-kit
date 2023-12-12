<?php

namespace App\Models\Interfaces;

use Exception;

interface SafelySaveInterface
{

    /**
     * @throws Exception
     */
    public function safelySave(array $attributes, array $options = []): bool;

    public function fillAndSave(array $attributes, array $options = []): bool;
}
