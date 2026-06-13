<?php

namespace Webafra\LaraSetting;

use Webafra\LaraSetting\Models\Setting as SettingModel;
use Illuminate\Support\Facades\Cache;

class Setting
{
    public function set(string $key, mixed $value, bool $is_primary = false, string $group = 'general'): mixed
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }

        Cache::forget($this->cacheKey($group, $key));

        SettingModel::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'is_primary' => $is_primary]
        );

        Cache::forever($this->cacheKey($group, $key), $value);

        return $value;
    }

    public function get(string $key, mixed $default = null, string $group = 'general'): mixed
    {
        return Cache::rememberForever($this->cacheKey($group, $key), function () use ($key, $default, $group) {
            return SettingModel::where('group', $group)->where('key', $key)->value('value') ?? $default;
        });
    }

    public function has(string $key, string $group = 'general'): bool
    {
        return SettingModel::where('group', $group)->where('key', $key)->exists();
    }

    public function delete(string $key, string $group = 'general'): bool
    {
        Cache::forget($this->cacheKey($group, $key));

        return (bool) SettingModel::where('group', $group)->where('key', $key)->delete();
    }

    public function getPrimary(mixed $default = null): mixed
    {
        return Cache::rememberForever('setting_primary', function () {
            return SettingModel::where('is_primary', true)->pluck('value', 'key')->toArray();
        }) ?? $default;
    }

    public function getGroup(string $group): array
    {
        return Cache::rememberForever('setting_group_' . $group, function () use ($group) {
            return SettingModel::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    public function store(array $settings, string $group = 'general'): int
    {
        $i = 0;
        foreach ($settings as $key => $value) {
            $this->set($key, $value, false, $group);
            $i++;
        }
        return $i;
    }

    public function storePrimary(array $settings, string $group = 'general'): int
    {
        $i = 0;
        foreach ($settings as $key => $value) {
            $this->set($key, $value, true, $group);
            $i++;
        }
        return $i;
    }

    public function clean(): void
    {
        Cache::forget('setting_primary');

        $settings = SettingModel::select('group', 'key')->get();

        foreach ($settings as $setting) {
            Cache::forget($this->cacheKey($setting->group, $setting->key));
            Cache::forget('setting_group_' . $setting->group);
        }
    }

    private function cacheKey(string $group, string $key): string
    {
        return 'setting_' . $group . '_' . $key;
    }
}
