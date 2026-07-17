<?php

namespace BookStack\Activity\Controllers;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Activity;
use BookStack\Http\Controller;
use BookStack\Permissions\Permission;
use BookStack\Sorting\SortUrl;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->checkPermission(Permission::SettingsManage);
        $this->checkPermission(Permission::UsersManage);

        $sort = $request->input('sort', 'activity_date');
        $order = $request->input('order', 'desc');
        $listOptions = (new SimpleListOptions('', $sort, $order))->withSortOptions([
            'created_at' => trans('settings.audit_table_date'),
            'type' => trans('settings.audit_table_event'),
        ]);

        $filters = [
            'event'     => $request->input('event', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to'   => $request->input('date_to', ''),
            'user'      => $request->input('user', ''),
            'ip'        => $request->input('ip', ''),
        ];

        $query = Activity::query()
            ->with([
                'loggable' => fn ($query) => $query->withTrashed(),
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
            'filterSortUrl' => new SortUrl('settings/audit', array_filter($request->except('page')))
        ]);
    }
}
