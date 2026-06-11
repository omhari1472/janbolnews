<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BreakingNews extends Model {
    protected $table = 'breaking_news';
    protected $fillable = ['text_hi','text_en','url','is_active','sort_order'];
    protected $casts = ['is_active' => 'boolean'];
}
