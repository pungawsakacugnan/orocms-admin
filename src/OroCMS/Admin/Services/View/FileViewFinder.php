<?php
namespace OroCMS\Admin\Services\View;

use Illuminate\View\FileViewFinder as BaseFileViewFinder;

class FileViewFinder extends BaseFileViewFinder
{
    /**
     * Prepend a location to the finder.
     *
     * @param  string  $location
     * @return void
     */
    public function prependLocation($location)
    {
        $this->paths = array_merge(is_array($location) 
            ? $location 
            : [$location], $this->paths);
    }
}