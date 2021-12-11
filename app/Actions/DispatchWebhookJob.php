<?php

namespace BookStack\Actions;

use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use BookStack\Interfaces\Loggable;
use BookStack\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Psr\Http\Client\ClientExceptionInterface;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $httpClient = new Client([
            'timeout' => 3,
            'allow_redirects' => ['strict' => true],
        ]);

        $request = new Request('POST', $this->webhook->endpoint, [
            'Content-Type' => 'application/json'
        ], json_encode($this->buildWebhookData()));

        try {
            $response = $httpClient->send($request);
            if ($response->getStatusCode() >= 400) {
                Log::error("Webhook call to endpoint {$this->webhook->endpoint} failed with status {$response->getStatusCode()}");
            }
        } catch (ClientExceptionInterface $exception) {
            Log::error("Received error during webhook call to endpoint {$this->webhook->endpoint}: {$exception->getMessage()}");
        }
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

        $data =  [
            'event' => $this->event,
            'text' => implode(' ', $textParts),
            'triggered_at' => Carbon::createFromTimestampUTC($this->initiatedTime)->toISOString(),
            'triggered_by' => $this->initiator->attributesToArray(),
            'triggered_by_profile_url' => $this->initiator->getProfileUrl(),
            'webhook_id' => $this->webhook->id,
            'webhook_name' => $this->webhook->name,
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
