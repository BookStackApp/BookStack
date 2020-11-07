<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{

    public function index(Request $request)
    {
        $this->checkPermission('settings-manage');
        $this->checkPermission('users-manage');

        $listDetails = [
            'order' => $request->get('order', 'desc'),
            'event' => $request->get('event', ''),
            'sort' => $request->get('sort', 'created_at'),
            'date_from' => $request->get('date_from', ''),
            'date_to' => $request->get('date_to', ''),
        ];

        $query = Activity::query()
            ->with([
                'entity' => function ($query) {
                    $query->withTrashed();
                },
                'user'
            ])
            ->orderBy($listDetails['sort'], $listDetails['order']);

        if ($listDetails['event']) {
            $query->where('key', '=', $listDetails['event']);
        }

        if ($listDetails['date_from']) {
            $query->where('created_at', '>=', $listDetails['date_from']);
        }
        if ($listDetails['date_to']) {
            $query->where('created_at', '<=', $listDetails['date_to']);
        }

        $activities = $query->paginate(100);
        $activities->appends($listDetails);

        $keys = DB::table('activities')->select('key')->distinct()->pluck('key');
        $this->setPageTitle(trans('settings.audit'));
        return view('settings.audit', [
            'activities' => $activities,
            'listDetails' => $listDetails,
            'activityKeys' => $keys,
        ]);
    }
}
