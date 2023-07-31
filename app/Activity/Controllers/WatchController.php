<?php

namespace BookStack\Activity\Controllers;

use BookStack\Activity\Models\Watch;
use BookStack\App\Model;
use BookStack\Entities\Models\Entity;
use BookStack\Http\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WatchController extends Controller
{
    public function update(Request $request)
    {
        $requestData = $this->validate($request, [
            'level' => ['required', 'string'],
        ]);

        $watchable = $this->getValidatedModelFromRequest($request);
        $newLevel = Watch::optionNameToLevel($requestData['level']);

        if ($newLevel < 0) {
            // TODO - Delete
        } else {
            // TODO - Upsert
        }
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    protected function getValidatedModelFromRequest(Request $request): Entity
    {
        $modelInfo = $this->validate($request, [
            'type' => ['required', 'string'],
            'id'   => ['required', 'integer'],
        ]);

        if (!class_exists($modelInfo['type'])) {
            throw new Exception('Model not found');
        }

        /** @var Model $model */
        $model = new $modelInfo['type']();
        if (!$model instanceof Entity) {
            throw new Exception('Model not an entity');
        }

        $modelInstance = $model->newQuery()
            ->where('id', '=', $modelInfo['id'])
            ->first(['id', 'name', 'owned_by']);

        $inaccessibleEntity = ($modelInstance instanceof Entity && !userCan('view', $modelInstance));
        if (is_null($modelInstance) || $inaccessibleEntity) {
            throw new Exception('Model instance not found');
        }

        return $modelInstance;
    }
}
