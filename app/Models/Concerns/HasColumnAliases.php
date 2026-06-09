<?php

namespace App\Models\Concerns;

trait HasColumnAliases
{
    public function getAttribute($key)
    {
        return parent::getAttribute($this->columnAliases[$key] ?? $key);
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute($this->columnAliases[$key] ?? $key, $value);
    }
}
