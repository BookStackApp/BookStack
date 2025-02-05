<?php

namespace BookStack\Sorting;

use BookStack\Activity\ActivityType;
use BookStack\Http\Controller;
use BookStack\Http\Request;

class SortSetController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:settings-manage');
        // TODO - Test
    }

    public function create()
    {
        $this->setPageTitle(trans('settings.sort_set_create'));

        return view('settings.sort-sets.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:1', 'max:200'],
            'sequence' => ['required', 'string', 'min:1'],
        ]);

        $operations = SortSetOperation::fromSequence($request->input('sequence'));
        if (count($operations) === 0) {
            return redirect()->withInput()->withErrors(['sequence' => 'No operations set.']);
        }

        $set = new SortSet();
        $set->name = $request->input('name');
        $set->setOperations($operations);
        $set->save();

        $this->logActivity(ActivityType::SORT_SET_CREATE, $set);

        return redirect('/settings/sorting');
    }

    public function edit(string $id)
    {
        $set = SortSet::query()->findOrFail($id);

        $this->setPageTitle(trans('settings.sort_set_edit'));

        return view('settings.sort-sets.edit', ['set' => $set]);
    }

    public function update(string $id, Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:1', 'max:200'],
            'sequence' => ['required', 'string', 'min:1'],
        ]);

        $set = SortSet::query()->findOrFail($id);
        $operations = SortSetOperation::fromSequence($request->input('sequence'));
        if (count($operations) === 0) {
            return redirect($set->getUrl())->withInput()->withErrors(['sequence' => 'No operations set.']);
        }

        $set->name = $request->input('name');
        $set->setOperations($operations);
        $set->save();

        $this->logActivity(ActivityType::SORT_SET_UPDATE, $set);

        return redirect('/settings/sorting');
    }

    public function destroy(string $id)
    {
        $set = SortSet::query()->findOrFail($id);

        if ($set->books()->count() > 0) {
            $this->showErrorNotification(trans('settings.sort_set_delete_fail_books'));
            return redirect($set->getUrl());
        }

        $defaultBookSortSetting = intval(setting('sorting-book-default', '0'));
        if ($defaultBookSortSetting === intval($id)) {
            $this->showErrorNotification(trans('settings.sort_set_delete_fail_default'));
            return redirect($set->getUrl());
        }

        $set->delete();
        $this->logActivity(ActivityType::SORT_SET_DELETE, $set);

        return redirect('/settings/sorting');
    }
}
