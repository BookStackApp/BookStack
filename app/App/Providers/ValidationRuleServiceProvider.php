<?php

namespace BookStack\App\Providers;

use BookStack\Uploads\ImageService;
use BookStack\Util\UrlFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidationRuleServiceProvider extends ServiceProvider
{
    /**
     * Register our custom validation rules when the application boots.
     */
    public function boot(): void
    {
        Validator::extend('image_extension', function ($attribute, $value, $parameters, $validator) {
            $extension = strtolower($value->getClientOriginalExtension());

            return ImageService::isExtensionSupported($extension);
        });

        Validator::extend('safe_url', function ($attribute, $value, $parameters, $validator) {
            $cleanLinkName = strtolower(trim($value));
            $filter = new UrlFilter($cleanLinkName);
            return $filter->isAllowed();
        });
    }
}
