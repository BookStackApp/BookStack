<?php

namespace BookStack\Activity\Controllers;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Tools\MixedEntityRequestHelper;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    public function update(Request $request, MixedEntityRequestHelper $entityHelper)
    {
        $this->checkPermission('receive-notifications');
        $this->preventGuestAccess();

        $requestData = $this->validate($request, array_merge([
            'level' => ['required', 'string'],
        ], $entityHelper->validationRules()));

        $watchable = $entityHelper->getVisibleEntityFromRequestData($requestData);
        $watchOptions = new UserEntityWatchOptions(user(), $watchable);
        $watchOptions->updateLevelByName($requestData['level']);

        $this->showSuccessNotification(trans('activities.watch_update_level_notification'));

        return redirect($watchable->getUrl());
    }
}
