<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use App\Helpers\ModelHelperConfig;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, ModelHelper, HasFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'release_at',
        'slug',
        'preview',
        'banner_preview',
        'description',
        'overview',
        'output',
        'client',
        'preview_link',
        'is_active',
        'is_featured',
        'meta',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'json',
        'release_at' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];


    public function modelHelperConfig(): modelHelperConfig
    {
        return ModelHelperConfig::create()
            ->generateSlug('title')
            ->files(['banner_preview', 'preview']);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->whereType('project');
    }
}
