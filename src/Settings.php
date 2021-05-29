<?php

namespace Smartisan\Settings;

use Smartisan\Settings\Exceptions\CastHandlerException;

class Settings
{
    /**
     * Settings repository instance.
     *
     * @var \Smartisan\Settings\Contracts\Repository
     */
    protected $repository;

    /**
     * Application cache repository instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * @var \Smartisan\Settings\EntryFilter
     */
    protected $filter;

    /**
     * Create a new settings manager instance.
     *
     * @param \Smartisan\Settings\Contracts\Repository $repository
     * @param \Illuminate\Contracts\Cache\Repository $cache
     */
    public function __construct($repository, $cache)
    {
        $this->repository = $repository;

        $this->cache = $cache;

        $this->filter = app(EntryFilter::class);
    }

    /**
     * Store settings entry for the given key.
     * The configured values of entry filter will be used to filter the settings entries.
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $this->forgetCacheIfEnabled($key);

        $this->repository
            ->withFilter($this->filter)
            ->set($key, $value);

        $this->filter->clear();
    }

    /**
     * Retrieve settings entry for the given key.
     * The configured values of entry filter will be used to filter the settings entries.
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (config('settings.cache.enabled')) {
            return $this->cache->rememberForever($this->resolveCacheKey($key), function () use ($key, $default) {
                return $this->getEntries($key, $default);
            });
        }

        return $this->getEntries($key, $default);
    }

    /**
     * Destroy the settings entry for the given key.
     *
     * @param string|array $key
     * @return void
     */
    public function forget($key)
    {
        $this->forgetCacheIfEnabled($key);

        $this->repository
            ->withFilter($this->filter)
            ->forget($key);

        $this->filter->clear();
    }

    /**
     * Retrieve all settings entry.
     * The configured values of entry filter will be used to filter the settings entries.
     *
     * @return array
     */
    public function all()
    {
        if (config('settings.cache.enabled')) {
            return $this->cache->rememberForever($this->resolveCacheKey(null), function () {
                return $this->getAllEntries();
            });
        }

        return $this->getAllEntries();
    }

    /**
     * Determines whether the given settings entry exists or not.
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        $entry = $this->get($key);

        return isset($entry);
    }

    /**
     * Set the model owner of the settings entry.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Smartisan\Settings\Settings
     */
    public function for($model)
    {
        $this->filter->setModel($model);

        return $this;
    }

    /**
     * Set the group name of the settings entry.
     *
     * @param string $name
     * @return \Smartisan\Settings\Settings
     */
    public function group($name)
    {
        $this->filter->setGroup($name);

        return $this;
    }

    /**
     * Set the exempted settings entries.
     *
     * @param string|array $excepts
     * @return \Smartisan\Settings\Settings
     */
    public function except(...$excepts)
    {
        $this->filter->setExcepts(...$excepts);

        return $this;
    }

    /**
     * Resolve settings entry caching key.
     *
     * @param string|array|null $keys
     * @return string
     */
    public function resolveCacheKey($keys)
    {
        $prefix = config('settings.cache.prefix');

        $keys = is_array($keys) ? implode(',', $keys) : $keys;

        $group = $this->filter->getGroup();

        $for = $this->filter->getModel() ? get_class($this->filter->getModel()) : null;

        $excepts = implode(',', $this->filter->getExcepts());

        return "${prefix}settings.keys=${keys}&group=${group}&excepts=${excepts}&for=$for";
    }

    /**
     * Retrieve the evalulated settings entries for the given key.
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed
     */
    protected function getEntries($key, $default)
    {
        $payload = $this->repository
            ->withFilter($this->filter)
            ->get($key, $default);

        $this->filter->clear();

        $this->stripSettingsPayload($payload);

        return $payload;
    }

    /**
     * Retrieve all settings entries.
     *
     * @return array
     */
    protected function getAllEntries()
    {
        $payload = $this->repository
            ->withFilter($this->filter)
            ->all();

        $this->filter->clear();

        $this->stripSettingsPayload($payload);

        return $payload;
    }

    /**
     * Clear the given caching key values.
     *
     * @param string $key
     * @return void
     */
    protected function forgetCacheIfEnabled($key)
    {
        if (config('settings.cache.enabled')) {
            $cacheKey = $this->resolveCacheKey(is_array($key) ? array_keys($key) : $key);

            if ($this->cache->has($cacheKey)) {
                $this->cache->forget($cacheKey);
            }
        }
    }

    /**
     * Evaluate the payload and strip additional attributes.
     *
     * @param mixed $payload
     * @return array
     */
    protected function stripSettingsPayload(&$payload)
    {
        if (is_array($payload)) {
            if (array_key_exists('$value', $payload) && array_key_exists('$cast', $payload)) {
                $castType = $payload['$cast'];

                if ($castType) {
                    if (! array_key_exists($castType, config('settings.casts'))) {
                        throw CastHandlerException::missing($castType);
                    }

                    return $payload = app(config('settings.casts')[$castType])
                        ->get($payload['$value']);
                }

                $payload = $payload['$value'];
            } else {
                array_walk($payload, [$this, 'stripSettingsPayload']);
            }
        }
    }
}
