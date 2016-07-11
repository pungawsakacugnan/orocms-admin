<?php
namespace OroCMS\Admin\Contracts;

interface PluggableRepositoryInterface
{
    /**
     * Get all plugins.
     *
     * @return mixed
     */
    public function all();

    /**
     * Scan & get all available plugins.
     *
     * @return array
     */
    public function scan();

    /**
     * Get plugins as plugins collection instance.
     *
     * @return \Pingpong\plugins\Collection
     */
    public function toCollection();

    /**
     * Get scanned paths.
     *
     * @return array
     */
    public function getScanPaths();

    /**
     * Get list of enabled plugins.
     *
     * @return mixed
     */
    public function enabled();

    /**
     * Get list of disabled plugins.
     *
     * @return mixed
     */
    public function disabled();

    /**
     * Get count from all plugins.
     *
     * @return int
     */
    public function count();

    /**
     * Get all plugins in order of priority.
     *
     * @return mixed
     */
    public function getPrioritized();

    /**
     * Get plugins by the given status.
     *
     * @param int $status
     *
     * @return mixed
     */
    public function getByStatus($status);

    /**
     * Find a specific plugin.
     *
     * @param $name
     *
     * @return mixed
     */
    public function find($name);

    /**
     * Find a specific plugin. If there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return mixed
     */
    public function findOrFail($name);
}
