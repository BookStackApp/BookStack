<?php

namespace BookStack\Activity\Controllers;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Webhook;
use BookStack\Activity\Queries\WebhooksAllPaginatedAndSorted;
use BookStack\Http\Controller;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'can:settings-manage',
        ]);
    }

    /**
     * Show all webhooks configured in the system.
     */
    public function index(Request $request)
    {
        $listOptions = SimpleListOptions::fromRequest($request, 'webhooks')->withSortOptions([
            'name' => trans('common.sort_name'),
            'endpoint'  => trans('settings.webhooks_endpoint'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
            'active'     => trans('common.status'),
        ]);

        $webhooks = (new WebhooksAllPaginatedAndSorted())->run(20, $listOptions);
        $webhooks->appends($listOptions->getPaginationAppends());

        $this->setPageTitle(trans('settings.webhooks'));

        return view('settings.webhooks.index', [
            'webhooks'    => $webhooks,
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Show the view for creating a new webhook in the system.
     */
    public function create()
    {
        $this->setPageTitle(trans('settings.webhooks_create'));

        return view('settings.webhooks.create');
    }

    /**
     * Store a new webhook in the system.
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'name'     => ['required', 'max:150'],
            'endpoint' => ['required', 'url', 'max:500'],
            'events'   => ['required', 'array'],
            'active'   => ['required'],
            'timeout'  => ['required', 'integer', 'min:1', 'max:600'],
        ]);

        $webhook = new Webhook($validated);
        $webhook->active = $validated['active'] === 'true';
        $webhook->save();
        $webhook->updateTrackedEvents(array_values($validated['events']));

        $this->logActivity(ActivityType::WEBHOOK_CREATE, $webhook);

        return redirect('/settings/webhooks');
    }

    /**
     * Show the view to edit an existing webhook.
     */
    public function edit(string $id)
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::query()
            ->with('trackedEvents')
            ->findOrFail($id);

        $this->setPageTitle(trans('settings.webhooks_edit'));

        return view('settings.webhooks.edit', ['webhook' => $webhook]);
    }

    /**
     * Update an existing webhook with the provided request data.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validate($request, [
            'name'     => ['required', 'max:150'],
            'endpoint' => ['required', 'url', 'max:500'],
            'events'   => ['required', 'array'],
            'active'   => ['required'],
            'timeout'  => ['required', 'integer', 'min:1', 'max:600'],
        ]);

        /** @var Webhook $webhook */
        $webhook = Webhook::query()->findOrFail($id);

        $webhook->active = $validated['active'] === 'true';
        $webhook->fill($validated)->save();
        $webhook->updateTrackedEvents($validated['events']);

        $this->logActivity(ActivityType::WEBHOOK_UPDATE, $webhook);

        return redirect('/settings/webhooks');
    }

    /**
     * Show the view to delete a webhook.
     */
    public function delete(string $id)
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::query()->findOrFail($id);

        $this->setPageTitle(trans('settings.webhooks_delete'));

        return view('settings.webhooks.delete', ['webhook' => $webhook]);
    }

    /**
     * Destroy a webhook from the system.
     */
    public function destroy(string $id)
    {
        /** @var Webhook $webhook */
        $webhook = Webhook::query()->findOrFail($id);

        $webhook->trackedEvents()->delete();
        $webhook->delete();

        $this->logActivity(ActivityType::WEBHOOK_DELETE, $webhook);

        return redirect('/settings/webhooks');
    }
}
