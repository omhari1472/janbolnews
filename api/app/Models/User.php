<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable {
    use HasApiTokens;
    protected $fillable  = ['name','email','password','role','permissions','avatar_path','status'];
    protected $hidden    = ['password','remember_token'];
    protected $appends   = ['avatar_url'];
    protected $casts     = ['email_verified_at'=>'datetime','password'=>'hashed','permissions'=>'array'];

    protected function avatarUrl(): Attribute {
        return Attribute::make(
            get: fn() => $this->avatar_path ? asset('storage/'.$this->avatar_path) : null
        );
    }
}
