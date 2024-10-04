<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;
    /* Setting filling columns */
    protected $fillable = [
        'comment',
        'likes',
        'dislikes',
        'post_id',
        'user_id',
    ];
    /* Polymorphic relationships */
    public function rates()
    {
        return $this->morphToMany(Rate::class, 'ratable')->withTimestamps();
    }
    /* Relationships */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
