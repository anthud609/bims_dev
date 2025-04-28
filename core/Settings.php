<?php
namespace Core;

use Illuminate\Support\Facades\DB; // Or just Capsule directly

class Settings
{
    protected static array $cache = [];

    public static function get(string $key, $default = null)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $setting = \Illuminate\Database\Capsule\Manager::table('settings')
            ->where('key', $key)
            ->first();

        if ($setting) {
            $value = json_decode($setting->value, true) ?? $setting->value;
            self::$cache[$key] = $value;
            return $value;
        }

        return $default;
    }
}
