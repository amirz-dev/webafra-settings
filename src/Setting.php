<?php

namespace Webafra\LaraSetting;

use Webafra\LaraSetting\Models\Setting as SettingModel;
use Illuminate\Support\Facades\Cache;

class Setting
{
    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param bool $is_primary
     * @param string $group
     * @return mixed
     */
    public function set(string $key, $value, bool $is_primary = false, string $group = SettingModel::GROUP_CUSTOM)
    {
        Cache::forget('setting_' . $key);
        Cache::forget('setting_group_' . $group);

        SettingModel::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'is_primary' => $is_primary, 'group' => $group]
        );

        Cache::forever('setting_' . $key, $value);

        return $value;
    }

    /**
     * Get a setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            return SettingModel::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Get all primary settings.
     *
     * @param mixed $default
     * @return array|mixed
     */
    public function getPrimary($default = null)
    {
        return Cache::rememberForever('setting_primary', function () {
            return SettingModel::where('is_primary', true)->pluck('value', 'key')->toArray();
        }) ?? $default;
    }

    /**
     * Get settings by group.
     *
     * @param string $group
     * @return array
     */
    public function getByGroup(string $group): array
    {
        return Cache::rememberForever('setting_group_' . $group, function () use ($group) {
            return SettingModel::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Store multiple settings.
     *
     * @param array $settings
     * @param string $group
     * @return int
     */
    public function store(array $settings, string $group = SettingModel::GROUP_CUSTOM): int
    {
        $i = 0;
        foreach ($settings as $key => $value) {
            $this->set($key, $value, false, $group);
            $i++;
        }
        return $i;
    }

    /**
     * Store multiple primary settings.
     *
     * @param array $settings
     * @param string $group
     * @return int
     */
    public function storePrimary(array $settings, string $group = SettingModel::GROUP_CUSTOM): int
    {
        $i = 0;
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            }
            $this->set($key, $value, true, $group);
            $i++;
        }
        return $i;
    }

    /**
     * Clear all cache for settings.
     */
    public function clean(): void
    {
        Cache::forget('setting_primary');

        foreach (SettingModel::pluck('key') as $key) {
            Cache::forget('setting_' . $key);
        }

        foreach (SettingModel::distinct()->pluck('group') as $group) {
            Cache::forget('setting_group_' . $group);
        }
    }
}