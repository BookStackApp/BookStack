<?php

namespace BookStack\Activity;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Models\Webhook;
use BookStack\Activity\Tools\WebhookFormatter;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Webhook $webhook;
    protected string $event;
    protected User $initiator;
    protected int $initiatedTime;

    /**
     * @var string|Loggable
     */
    protected $detail;

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
        $themeResponse = Theme::dispatch(ThemeEvents::WEBHOOK_CALL_BEFORE, $this->event, $this->webhook, $this->detail, $this->initiator, $this->initiatedTime);
        $webhookData = $themeResponse ?? WebhookFormatter::getDefault($this->event, $this->webhook, $this->detail, $this->initiator, $this->initiatedTime)->format();
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
}
