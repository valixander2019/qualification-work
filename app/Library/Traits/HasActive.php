<?php


namespace App\Library\Traits;


trait HasActive
{
    /**
     * Только активные записи
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
