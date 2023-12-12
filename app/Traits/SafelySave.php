<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait SafelySave
{

    /**
     * @throws Exception
     */
    public function safelySave(array $attributes, array $options = []): bool
    {
        DB::beginTransaction();

        try {
            $return = $this->fillAndSave($attributes, $options);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $return;
    }

    public function fillAndSave(array $attributes, array $options = []): bool
    {
        if ($this->getGuarded() != ['*']) {
            $this->fill(Arr::except($attributes, $this->getGuarded()));
        } else {
            $this->fill(Arr::only($attributes, $this->getFillable()));
        }

        return self::save($options);
    }
}
