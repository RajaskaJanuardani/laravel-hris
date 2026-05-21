<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class SystemSetting extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];
 
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
 
        return match($setting->type) {
            'integer' => (int) $setting->value,
            'decimal' => (float) $setting->value,
            'boolean' => $setting->value === 'true' || $setting->value === '1',
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
 
    public static function set($key, $value, $type = 'string', $description = null)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) ? json_encode($value) : (string) $value;
        $setting->type = $type;
        if ($description) {
            $setting->description = $description;
        }
        $setting->save();
        return $setting;
    }
}