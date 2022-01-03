<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityType;
use BookStack\Actions\Webhook;
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
    public function index()
    {
        $webhooks = Webhook::query()
            ->orderBy('name', 'desc')
            ->with('trackedEvents')
            ->get();

        return view('settings.webhooks.index', ['webhooks' => $webhooks]);
    }

    /**
     * Show the view for creating a new webhook in the system.
     */
    public function create()
    {
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
