<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'mobile',
        'token',
        'otp_code',
        'used',
        'attempts',
        'expires_at',
        'form_fields'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
