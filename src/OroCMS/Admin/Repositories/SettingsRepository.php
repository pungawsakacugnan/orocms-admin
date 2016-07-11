<?php
namespace OroCMS\Admin\Repositories;

use OroCMS\Admin\Entities\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

class SettingsRepository
{
    /**
     * @var
     */
    protected $settings;

    /**
     * @param OroCMS\Admin\Repositories\SettingsRepository $repository
     */
    function __construct()
    {
        $this->settings = $this->getModel()
            ->all();
    }

    /**
     * Get model.
     *
     * @param boolean
     *
     * @return \OroCMS\Admin\Entities\Settings
     */
    public function getModel()
    {
        return new Settings;
    }

    /**
     * Get all settings.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function settings($key = null)
    {
        if ($key) {
            return $this->{$key};
        }

        return $this;
    }

    /**
     * Update settings.
     *
     * @param array
     */
    public function update()
    {
        $data = Request::get('settings', []);

        if (empty($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            !empty($key) && $this->getModel()->updateOrCreate(['key' => $key], [
                    'key' => $key,
                    'value' => $value
                ]);
        }
    }

    /**
     * Get properties.
     */
    public function __get($attribute)
    {
        // attempt to fetch from settings object
        if ($this->settings) {
            $record = $this->settings->where('key', $attribute)
                ->first();

            if ($record) {
                return $record->value;
            }
        }
    }
}