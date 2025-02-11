<?php

namespace BookStack\Sorting;

use BookStack\Activity\ActivityType;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class SortRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:settings-manage');
    }

    public function create()
    {
        $this->setPageTitle(trans('settings.sort_rule_create'));

        return view('settings.sort-rules.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:1', 'max:200'],
            'sequence' => ['required', 'string', 'min:1'],
        ]);

        $operations = SortRuleOperation::fromSequence($request->input('sequence'));
        if (count($operations) === 0) {
            return redirect()->withInput()->withErrors(['sequence' => 'No operations set.']);
        }

        $rule = new SortRule();
        $rule->name = $request->input('name');
        $rule->setOperations($operations);
        $rule->save();

        $this->logActivity(ActivityType::SORT_RULE_CREATE, $rule);

        return redirect('/settings/sorting');
    }

    public function edit(string $id)
    {
        $rule = SortRule::query()->findOrFail($id);

        $this->setPageTitle(trans('settings.sort_rule_edit'));

        return view('settings.sort-rules.edit', ['rule' => $rule]);
    }

    public function update(string $id, Request $request, BookSorter $bookSorter)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:1', 'max:200'],
            'sequence' => ['required', 'string', 'min:1'],
        ]);

        $rule = SortRule::query()->findOrFail($id);
        $operations = SortRuleOperation::fromSequence($request->input('sequence'));
        if (count($operations) === 0) {
            return redirect($rule->getUrl())->withInput()->withErrors(['sequence' => 'No operations set.']);
        }

        $rule->name = $request->input('name');
        $rule->setOperations($operations);
        $changedSequence = $rule->isDirty('sequence');
        $rule->save();

        $this->logActivity(ActivityType::SORT_RULE_UPDATE, $rule);

        if ($changedSequence) {
            $bookSorter->runBookAutoSortForAllWithSet($rule);
        }

        return redirect('/settings/sorting');
    }

    public function destroy(string $id, Request $request)
    {
        $rule = SortRule::query()->findOrFail($id);
        $confirmed = $request->input('confirm') === 'true';
        $booksAssigned = $rule->books()->count();
        $warnings = [];

        if ($booksAssigned > 0) {
            if ($confirmed) {
                $rule->books()->update(['sort_rule_id' => null]);
            } else {
                $warnings[] = trans('settings.sort_rule_delete_warn_books', ['count' => $booksAssigned]);
            }
        }

        $defaultBookSortSetting = intval(setting('sorting-book-default', '0'));
        if ($defaultBookSortSetting === intval($id)) {
            if ($confirmed) {
                setting()->remove('sorting-book-default');
            } else {
                $warnings[] = trans('settings.sort_rule_delete_warn_default');
            }
        }

        if (count($warnings) > 0) {
            return redirect($rule->getUrl() . '#delete')->withErrors(['delete' => $warnings]);
        }

        $rule->delete();
        $this->logActivity(ActivityType::SORT_RULE_DELETE, $rule);

        return redirect('/settings/sorting');
    }
}
