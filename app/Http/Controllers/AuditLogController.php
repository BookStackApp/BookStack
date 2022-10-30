<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\Activity;
use BookStack\Actions\ActivityType;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->checkPermission('settings-manage');
        $this->checkPermission('users-manage');

        $sort = $request->get('sort', 'activity_date');
        $order = $request->get('order', 'desc');
        $listOptions = (new SimpleListOptions('', $sort, $order))->withSortOptions([
            'created_at' => trans('settings.audit_table_date'),
            'type' => trans('settings.audit_table_event'),
        ]);

        $filters = [
            'event'     => $request->get('event', ''),
            'date_from' => $request->get('date_from', ''),
            'date_to'   => $request->get('date_to', ''),
            'user'      => $request->get('user', ''),
            'ip'        => $request->get('ip', ''),
        ];

        $query = Activity::query()
            ->with([
                'entity' => fn ($query) => $query->withTrashed(),
                'user',
            ])
            ->orderBy($listOptions->getSort(), $listOptions->getOrder());

        if ($filters['event']) {
            $query->where('type', '=', $filters['event']);
        }
        if ($filters['user']) {
            $query->where('user_id', '=', $filters['user']);
        }

        if ($filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        if ($filters['ip']) {
            $query->where('ip', 'like', $filters['ip'] . '%');
        }

        $activities = $query->paginate(100);
        $activities->appends($request->all());

        $types = ActivityType::all();
        $this->setPageTitle(trans('settings.audit'));

        return view('settings.audit', [
            'activities'    => $activities,
            'filters'       => $filters,
            'listOptions'   => $listOptions,
            'activityTypes' => $types,
        ]);
    }
}
