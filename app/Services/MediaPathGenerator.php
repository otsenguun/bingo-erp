<?php

namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaPathGenerator extends DefaultPathGenerator
{

    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $path = config('media-library.prefix', '');

        if ($path !== '') {
            $path .= '/';
        }

        if ($folder = $media->getCustomProperty('path')) {
            $path .= config('tenancy.filesystem.suffix_base').tenant()->id.'/';
            $path .= $folder . '/';
        }

        return $path . $media->getKey();
    }
}