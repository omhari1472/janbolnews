<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Article extends Model {
    protected $fillable = [
        'title_hi','title_en','slug','excerpt_hi','excerpt_en',
        'content_hi','content_en','featured_image','category_id','author_id',
        'status','language','is_featured','is_breaking','views','published_at',
    ];
    protected $casts = [
        'is_featured'  => 'boolean',
        'is_breaking'  => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category() { return $this->belongsTo(Category::class); }
    public function author()   { return $this->belongsTo(Author::class); }
    public function tags()     { return $this->belongsToMany(Tag::class, 'article_tags'); }

    public function scopePublished($q) { return $q->where('status','published'); }
    public function scopeFeatured($q)  { return $q->where('is_featured', true); }
    public function scopeBreaking($q)  { return $q->where('is_breaking', true); }
}
