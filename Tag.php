<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    /* Setting filling columns */
    protected $fillable = ['title'];
    /* Relationships */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
    /* Add slug for title */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
    /* Mutator of title */
    public function setTitleAttribute($value)
    {
        return $this->attributes['title'] = Str::lower($value);
    }

}
