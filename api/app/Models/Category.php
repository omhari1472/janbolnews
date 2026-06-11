<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    protected $fillable = ['name_hi','name_en','slug','icon','color','sort_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function articles() { return $this->hasMany(Article::class); }
}
