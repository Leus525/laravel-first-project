<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use HasFactory;
    use SoftDeletes;
    /* Setting filling columns */
    protected $fillable = [
        'reply',
        'likes',
        'dislikes',
        'user_id',
        'comment_id',
        'post_id',
    ];
    /* Polymorphic Relationships */
    public function rates()
    {
        return $this->morphToMany(Rate::class, 'ratable')->withTimestamps();
    }
    /* Relationships */
    public function comment()
    {
        return $this->belongsTo(Comment::class)->withTrashed();
    }
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function post()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
