<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    /* Setting filling columns */
    protected $fillable = [
        'title',
        'description',
        'text',
        'thumbnail',
        'views',
        'category_id'
    ];

    /* Relationships */
    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id')
            ->withTimestamps()->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /* Add slug for title */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /* Mutator of title */
    public function setTitleAttribute($value)
    {
        if (!str_contains($value, '</')) {
            return $this->attributes['title'] = Str::title($value);
        } else {
            return $this->attributes['title'] = $value;
        }

    }

    public static function uploadFile(Request $request, $file = null)
    {
        if ($request->hasFile('thumbnail')) {
            $file ? Storage::delete($file) : '';
            return $request->file('thumbnail')->store("image/" . date('Y-m-d'));
        } elseif ($request->image) {
            $file ? Storage::delete($file) : '';
            return $file = null;
        } else {
            return $file;
        }

    }

    public static function getFile($file = null)
    {
        if (isset($file)) {
            print_r(asset('storage/' . $file));
        } else {
            print_r('/storage/app/image/thumbnail.jpg');
        }
    }

    public function scopeSearch($query, $s)
    {
        return $query->where('title', 'LIKE', '%' . $s . '%')->with('category')->paginate(3);
    }
}
