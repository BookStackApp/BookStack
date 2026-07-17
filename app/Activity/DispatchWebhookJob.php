<?php

namespace BookStack\Activity;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Models\Webhook;
use BookStack\Activity\Tools\WebhookFormatter;
use BookStack\Facades\Theme;
use BookStack\Http\HttpRequestService;
use BookStack\Theming\ThemeEvents;
use BookStack\Users\Models\User;
use BookStack\Util\SsrUrlValidator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Webhook $webhook;
    protected User $initiator;
    protected int $initiatedTime;
    protected array $webhookData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Webhook $webhook, string $event, Loggable|string $detail)
    {
        $this->webhook = $webhook;
        $this->initiator = user();
        $this->initiatedTime = time();

        $themeResponse = Theme::dispatch(ThemeEvents::WEBHOOK_CALL_BEFORE, $event, $this->webhook, $detail, $this->initiator, $this->initiatedTime);
        $this->webhookData =  $themeResponse ?? WebhookFormatter::getDefault($event, $this->webhook, $detail, $this->initiator, $this->initiatedTime)->format();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(HttpRequestService $http)
    {
        $lastError = null;

        try {
            (new SsrUrlValidator())->ensureAllowed($this->webhook->endpoint);

            $client = $http->buildClient($this->webhook->timeout, [
                'connect_timeout' => 10,
                'allow_redirects' => ['strict' => true],
            ]);

            $response = $client->sendRequest($http->jsonRequest('POST', $this->webhook->endpoint, $this->webhookData));
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 400) {
                $lastError = "Response status from endpoint was {$statusCode}";
                Log::error("Webhook call to endpoint {$this->webhook->endpoint} failed with status {$statusCode}");
            }
        } catch (\Exception $error) {
            $lastError = $error->getMessage();
            Log::error("Webhook call to endpoint {$this->webhook->endpoint} failed with error \"{$lastError}\"");
        }

        $this->webhook->last_called_at = now();
        if ($lastError) {
            $this->webhook->last_errored_at = now();
            $this->webhook->last_error = $lastError;
        }

        $this->webhook->save();
    }
}
