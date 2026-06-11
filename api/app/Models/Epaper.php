<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Epaper extends Model {
    protected $fillable = ['title','edition_date','edition','pdf_path','thumbnail_path','is_active'];
    protected $casts = ['is_active' => 'boolean', 'edition_date' => 'date'];
}
