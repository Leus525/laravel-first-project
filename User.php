<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /* Relationships */
    public function comments()
    {
        return $this->hasMany(Comment::class)->withTrashed();
    }
    public function replies()
    {
        return $this->hasMany(Reply::class)->withTrashed();
    }
    /* Set avatar (decode image from base_64 to file and save) */
    public static function uploadFile(Request $request, $file = null)
    {
        /* Set new avatar */
        if (!(str_contains($request->avatar, 'storage/')) && $request->avatar) {
            isset($file) ? unlink($file) : '';
            $image_64 = $request->avatar;
            $extension = explode('/', explode(
                ':',
                substr($image_64, 0, strpos($image_64, ';'))
            )[1])[1];
            // .jpg .png .pdf
            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            Storage::disk('public')->put('avatars/' . $imageName, base64_decode($image));
            return $path = 'storage/avatars/' . $imageName;
        /* Leave existing avatar */
        } elseif (str_contains($request->avatar, 'storage/')) {
            isset($file) ? unlink($file) : '';
            return $path = $request->avatar;
        /* Delete avatar and put default image */
        } else {
            isset($file) ? unlink($file) : '';
            return $path = null;
        }

    }
    /* Get path of user avatar */
    public static function getFile($file = null)
    {
        if (isset($file)) {
            print_r(asset($file));
        } else {
            print_r(asset('/storage/avatars/avatar.png'));
        }
    }
}
