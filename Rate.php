<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    /* Setting filling columns */
    protected $fillable = [
        'user_id',
        'rate_type',
    ];

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* Polymorphic Relationships */
    public function comments()
    {
        return $this->morphedByMany(Comment::class, 'ratable')->withTimestamps();
    }

    public function replies()
    {
        return $this->morphedByMany(Reply::class, 'ratable')->withTimestamps();
    }
    /* Set rate of comment or reply */
    public static function rated($comment, $type)
    {
        $get = $comment->rates->where('user_id', '=', auth()->user()->id);
        if (count($get)) {
            foreach ($get as $rate) {
                /* Change from one to other rate */
                if ($rate->user_id == auth()->user()->id && $rate->pivot->ratable_id == $comment->id && $type !== $rate->rate_type) {
                    if ($type == 'like' && $rate->rate_type == 'dislike') {
                        $comment->update(['likes' => $comment->likes += 1, 'dislikes' => $comment->dislikes -= 1]);
                        $rate->update(['rate_type' => 'like']);
                        return redirect()->route('articles.one', ['slug' => Post::find($comment->post_id)->slug]);
                    } elseif ($type == 'dislike' && $rate->rate_type == 'like') {
                        $comment->update(['dislikes' => $comment->dislikes += 1, 'likes' => $comment->likes -= 1]);
                        $rate->update(['rate_type' => 'dislike']);
                        return redirect()->route('articles.one', ['slug' => Post::find($comment->post_id)->slug]);
                    }


                }
                /* undo like or dislike */
                else if (($rate->user_id == auth()->user()->id) && ($rate->pivot->ratable_id == $comment->id)
                    && $type === $rate->rate_type) {
                    $comm = $type == 'like' ? $comment->likes : $comment->dislikes;
                    $comm !== 0 ? $comm -= 1 : '';
                    $type == 'like' ? $comment->update(['likes' => $comm]) : $comment->update(['dislikes' => $comm]);
                    $comment->rates()->delete();
                    return redirect()->route('articles.one', ['slug' => Post::find($comment->post_id)->slug]);

                }

            }
        /* like or dislike */
        } else {
            $comm = $type == 'like' ? $comment->likes : $comment->dislikes;
            $comm += 1;
            $type == 'like' ? $comment->update(['likes' => $comm]) : $comment->update(['dislikes' => $comm]);
            $type == 'like' ? $comment->rates()->create(['user_id' => auth()->user()->id, 'rate_type' => 'like']) : $comment->rates()
                ->create(['user_id' => auth()->user()->id, 'rate_type' => 'dislike']);
        }
        return redirect()->route('articles.one', ['slug' => Post::find($comment->post_id)->slug]);
    }
}
