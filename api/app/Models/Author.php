<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Author extends Model {
    protected $fillable = ['name','bio_hi','bio_en','email','avatar_path','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function articles() { return $this->hasMany(Article::class); }
}
