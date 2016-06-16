<?php
namespace OroCMS\Admin\Traits;

use Illuminate\Support\Str;

trait SlugTrait
{
    /**
     * Create slug.
     *
     * @param mixed
     *
     * @return bool
     */
    public function toSlug()
    {
        $args = func_get_args();
        $context = array_shift($args);

        if (is_array($context)) {
            $replacement = '-';

            // if set, look for this key from array
            $fk = array_shift($args);

            switch (count($args)) {
                case 1:
                    $replacement = array_shift($args);
                    break;
            }

            $_ctx = @$context['slug'];
            if (!is_null($fk)) {
                empty($_ctx) and $_ctx = @$context[$fk];
            }

            $context = $_ctx;
        }
        else {
            $replacement = array_shift($args);
        }

        $replacement or $replacement = '-';
        is_array($replacement) and $replacement = implode(' ', $replacement);

        return Str::slug($context, $replacement);
    }
}
