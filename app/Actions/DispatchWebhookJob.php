<?php

namespace BookStack\Actions;

use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use BookStack\Facades\Theme;
use BookStack\Interfaces\Loggable;
use BookStack\Model;
use BookStack\Theming\ThemeEvents;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Webhook
     */
    protected $webhook;

    /**
     * @var string
     */
    protected $event;

    /**
     * @var string|Loggable
     */
    protected $detail;

    /**
     * @var User
     */
    protected $initiator;

    /**
     * @var int
     */
    protected $initiatedTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Webhook $webhook, string $event, $detail)
    {
        $this->webhook = $webhook;
        $this->event = $event;
        $this->detail = $detail;
        $this->initiator = user();
        $this->initiatedTime = time();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $themeResponse = Theme::dispatch(ThemeEvents::WEBHOOK_CALL_BEFORE, $this->event, $this->webhook, $this->detail);
        $webhookData = $themeResponse ?? $this->buildWebhookData();
        $lastError = null;

        try {
            $response = Http::asJson()
                ->withOptions(['allow_redirects' => ['strict' => true]])
                ->timeout($this->webhook->timeout)
                ->post($this->webhook->endpoint, $webhookData);
        } catch (\Exception $exception) {
            $lastError = $exception->getMessage();
            Log::error("Webhook call to endpoint {$this->webhook->endpoint} failed with error \"{$lastError}\"");
        }

        if (isset($response) && $response->failed()) {
            $lastError = "Response status from endpoint was {$response->status()}";
            Log::error("Webhook call to endpoint {$this->webhook->endpoint} failed with status {$response->status()}");
        }

        $this->webhook->last_called_at = now();
        if ($lastError) {
            $this->webhook->last_errored_at = now();
            $this->webhook->last_error = $lastError;
        }

        $this->webhook->save();
    }

    protected function buildWebhookData(): array
    {
        $textParts = [
            $this->initiator->name,
            trans('activities.' . $this->event),
        ];

        if ($this->detail instanceof Entity) {
            $textParts[] = '"' . $this->detail->name . '"';
        }

        $data = [
            'event'                    => $this->event,
            'text'                     => implode(' ', $textParts),
            'triggered_at'             => Carbon::createFromTimestampUTC($this->initiatedTime)->toISOString(),
            'triggered_by'             => $this->initiator->attributesToArray(),
            'triggered_by_profile_url' => $this->initiator->getProfileUrl(),
            'webhook_id'               => $this->webhook->id,
            'webhook_name'             => $this->webhook->name,
        ];

        if (method_exists($this->detail, 'getUrl')) {
            $data['url'] = $this->detail->getUrl();
        }

        if ($this->detail instanceof Model) {
            $data['related_item'] = $this->detail->attributesToArray();
        }

        return $data;
    }
}
