<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Epaper extends Model {
    protected $fillable = ['title','edition_date','edition','status','is_active'];
    protected $casts = ['is_active' => 'boolean', 'edition_date' => 'date'];

    public function articles() {
        return $this->belongsToMany(Article::class, 'epaper_articles')
                    ->withPivot('position')
                    ->orderBy('epaper_articles.position');
    }
}
