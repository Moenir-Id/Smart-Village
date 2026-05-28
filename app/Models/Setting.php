<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['key', 'value', 'group'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'umum'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("setting_{$key}");
    }

    public static function group(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->all();
    }

    public static function flush(): void
    {
        static::all()->each(fn($s) => Cache::forget("setting_{$s->key}"));
    }
}
