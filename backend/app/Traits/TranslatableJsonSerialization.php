<?php
namespace App\Traits;

trait TranslatableJsonSerialization
{
    /**
     * Override toArray to return translated strings instead of arrays.
     */
    public function toArray()
    {
        $array = parent::toArray();

        foreach ($this->translatable ?? [] as $attr) {
            if (isset($array[$attr]) && is_array($array[$attr])) {
                $array[$attr] = $array[$attr][app()->getLocale()] ?? $array[$attr]['en'] ?? null;
            }
        }

        return $array;
    }
}
