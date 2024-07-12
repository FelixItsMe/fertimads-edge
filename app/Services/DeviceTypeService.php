<?php

namespace App\Services;

class DeviceTypeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sanitizeDescription(string $description) : string {
        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><i><strong><ul><ol><li><a><blockquote>';

        // Remove disallowed tags and attributes
        $strip_description = strip_tags($description, $allowedTags);
        $strip_description = preg_replace('/<(.*?)>/i', '<$1>', $strip_description);

        return $strip_description;
    }
}
