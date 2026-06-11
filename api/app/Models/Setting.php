<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model {
    protected $fillable = ['key','value'];
    public $timestamps = false;
    public static function get(string $key, $default = null) {
        $row = static::where('key',$key)->first();
        return $row ? $row->value : $default;
    }
    public static function set(string $key, $value): void {
        static::updateOrCreate(['key'=>$key],['value'=>$value]);
    }
    public static function allAsObject(): object {
        $rows = static::all()->pluck('value','key');
        return (object) $rows->toArray();
    }
}
