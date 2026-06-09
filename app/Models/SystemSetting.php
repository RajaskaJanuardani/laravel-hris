<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class SystemSetting extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'pengaturan_sistem';

    protected array $columnAliases = [
        'key' => 'kunci',
        'value' => 'nilai',
        'type' => 'tipe',
        'description' => 'deskripsi',
    ];
 
    protected $fillable = [
        'key',
        'kunci',
        'value',
        'nilai',
        'type',
        'tipe',
        'description',
        'deskripsi',
    ];
 
    public static function get($key, $default = null)
    {
        $setting = self::where('kunci', $key)->first();
        
        if (!$setting) {
            return $default;
        }
 
        return match($setting->tipe) {
            'integer' => (int) $setting->nilai,
            'decimal' => (float) $setting->nilai,
            'boolean' => $setting->nilai === 'true' || $setting->nilai === '1',
            'json' => json_decode($setting->nilai, true),
            default => $setting->nilai,
        };
    }
 
    public static function set($key, $value, $type = 'string', $description = null)
    {
        $setting = self::firstOrNew(['kunci' => $key]);
        $setting->nilai = is_array($value) ? json_encode($value) : (string) $value;
        $setting->tipe = $type;
        if ($description) {
            $setting->deskripsi = $description;
        }
        $setting->save();
        return $setting;
    }
}
