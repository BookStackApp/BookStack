<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Models\Webhook;
use BookStack\App\Model;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Support\Carbon;

class WebhookFormatter
{
    protected Webhook $webhook;
    protected string $event;
    protected User $initiator;
    protected int $initiatedTime;
    protected string|Loggable $detail;

    /**
     * @var array{condition: callable(string, Model):bool, format: callable(Model):void}[]
     */
    protected $modelFormatters = [];

    public function __construct(string $event, Webhook $webhook, string|Loggable $detail, User $initiator, int $initiatedTime)
    {
        $this->webhook = $webhook;
        $this->event = $event;
        $this->initiator = $initiator;
        $this->initiatedTime = $initiatedTime;
        $this->detail = is_object($detail) ? clone $detail : $detail;
    }

    public function format(): array
    {
        $data = [
            'event'                    => $this->event,
            'text'                     => $this->formatText(),
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
            $data['related_item'] = $this->formatModel();
        }

        return $data;
    }

    /**
     * @param callable(string, Model):bool $condition
     * @param callable(Model):void         $format
     */
    public function addModelFormatter(callable $condition, callable $format): void
    {
        $this->modelFormatters[] = [
            'condition' => $condition,
            'format'    => $format,
        ];
    }

    public function addDefaultModelFormatters(): void
    {
        // Load entity owner, creator, updater details
        $this->addModelFormatter(
            fn ($event, $model) => ($model instanceof Entity),
            fn ($model) => $model->load(['ownedBy', 'createdBy', 'updatedBy'])
        );

        // Load revision detail for page update and create events
        $this->addModelFormatter(
            fn ($event, $model) => ($model instanceof Page && ($event === ActivityType::PAGE_CREATE || $event === ActivityType::PAGE_UPDATE)),
            fn ($model) => $model->load('currentRevision')
        );
    }

    protected function formatModel(): array
    {
        /** @var Model $model */
        $model = $this->detail;
        $model->unsetRelations();

        foreach ($this->modelFormatters as $formatter) {
            if ($formatter['condition']($this->event, $model)) {
                $formatter['format']($model);
            }
        }

        return $model->toArray();
    }

    protected function formatText(): string
    {
        $textParts = [
            $this->initiator->name,
            trans('activities.' . $this->event),
        ];

        if ($this->detail instanceof Entity) {
            $textParts[] = '"' . $this->detail->name . '"';
        }

        return implode(' ', $textParts);
    }

    public static function getDefault(string $event, Webhook $webhook, $detail, User $initiator, int $initiatedTime): self
    {
        $instance = new self($event, $webhook, $detail, $initiator, $initiatedTime);
        $instance->addDefaultModelFormatters();

        return $instance;
    }
}
