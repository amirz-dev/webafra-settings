<?php

namespace Webafra\LaraSetting;

use Webafra\LaraSetting\Models\Setting as SettingModel;
use Illuminate\Support\Facades\Cache;

class Setting
{
    public function set(string $key, $value, bool $is_primary = false)
    {
        Cache::forget('setting_' . $key);

        SettingModel::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'is_primary' => $is_primary]
        );

        Cache::forever('setting_' . $key, $value);

        return $value;
    }

    public function get(string $key, $default = null)
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            return SettingModel::where('key', $key)->value('value') ?? $default;
        });
    }

    public function getPrimary($default = null)
    {
        return Cache::rememberForever('setting_primary', function () {
            return SettingModel::where('is_primary', true)->pluck('value', 'key')->toArray();
        }) ?? $default;
    }

    public function store(array $settings): int
    {
        $i = 0;
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
            $i++;
        }
        return $i;
    }

    public function storePrimary(array $settings): int
    {
        $i = 0;
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            }
            $this->set($key, $value, true);
            $i++;
        }
        return $i;
    }

    public function clean(): void
    {
        Cache::forget('setting_primary');
        foreach (SettingModel::pluck('key') as $key) {
            Cache::forget('setting_' . $key);
        }
    }
}
