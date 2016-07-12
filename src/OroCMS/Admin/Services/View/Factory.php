<?php
namespace OroCMS\Admin\Services\View;

use Illuminate\View\Factory as FactoryContract;

class Factory extends FactoryContract
{
    /**
     * Prepend a location to the array of view locations.
     *
     * @param  string  $location
     * @return void
     */
    public function prependLocation($location)
    {
        $this->finder->prependLocation($location);
    }
}