<?php

namespace App\Models;

use App\Library\Traits\DefaultDatetimeFormat;
use App\Library\Traits\HasActive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasActive,
        DefaultDatetimeFormat;

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_id',
        'is_active',
        'title',
        'description',
        'code',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'breadcrumb'
    ];

    /**
     * Получить хлебные крошки
     *
     * @return array
     */
    public function getBreadcrumbAttribute()
    {
        $breadcrumb = $this->section->breadcrumb;

        $breadcrumb[ count($breadcrumb) - 1 ]['link'] = [
            'name' => 'sections.detail',
            'params' => [
                'id' => $this->section->getKey(),
            ]
        ];

        $breadcrumb[] = [
            'name' => $this->title,
        ];

        return $breadcrumb;
    }

    /**
     * Получить родителя
     *
     * @return BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
