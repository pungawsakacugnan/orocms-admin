<?php

if (! function_exists('theme')) {
    /**
     * Return theme path.
     *
     * @param  string  $type
     * @return string
     */
    function theme($type, $asset = '')
    {
        $options = explode('.', $type);

        $type = array_shift($options);
        $arg = array_shift($options);

        $theme = config('admin.themes.default_theme');
        if (strtolower($type) == 'admin') {
            $theme = config('admin.themes.cp.default_theme');
        }

    	$paths = [
            $arg ?: null,
            'themes',
            $theme
    	];

        // set type
        !empty($type) and array_splice($paths, 1, 0, $type);

        // add asset
        !empty($asset) and $paths[] = $asset;

        // remove empty
        $paths = array_filter($paths, function($path) {
            return !empty($path);
        });

        return implode('/', $paths);
    }
}
