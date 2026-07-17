<?php

namespace BookStack\App;

use BookStack\Http\ApiController;
use Illuminate\Http\JsonResponse;

class SystemApiController extends ApiController
{
    /**
     * Read details regarding the BookStack instance.
     * Some details may be null where not set, like the app logo for example.
     */
    public function read(): JsonResponse
    {
        $logoSetting = setting('app-logo', '');
        if ($logoSetting === 'none') {
            $logo = null;
        } else {
            $logo = $logoSetting ? url($logoSetting) : url('/logo.png');
        }

        return response()->json([
            'version' => AppVersion::get(),
            'instance_id' => setting('instance-id'),
            'app_name' => setting('app-name'),
            'app_logo' => $logo,
            'base_url' => url('/'),
        ]);
    }
}
