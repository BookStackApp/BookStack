<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\MessageParts\EntityPathMessageLine;
use BookStack\Activity\Notifications\MessageParts\LinkedMailMessageLine;
use BookStack\App\MailNotification;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Translation\LocaleDefinition;
use BookStack\Users\Models\User;
use Illuminate\Bus\Queueable;

abstract class BaseActivityNotification extends MailNotification
{
    use Queueable;

    public function __construct(
        protected Loggable|string $detail,
        protected User $user,
    ) {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'activity_detail' => $this->detail,
            'activity_creator' => $this->user,
        ];
    }

    /**
     * Build the common reason footer line used in mail messages.
     */
    protected function buildReasonFooterLine(LocaleDefinition $locale): LinkedMailMessageLine
    {
        return new LinkedMailMessageLine(
            url('/preferences/notifications'),
            $locale->trans('notifications.footer_reason'),
            $locale->trans('notifications.footer_reason_link'),
        );
    }

    /**
     * Build a line which provides the book > chapter path to a page.
     * Takes into account visibility of these parent items.
     * Returns null if no path items can be used.
     */
    protected function buildPagePathLine(Page $page, User $notifiable): ?EntityPathMessageLine
    {
        $permissions = new PermissionApplicator($notifiable);

        $path = array_filter([$page->book, $page->chapter], function (?Entity $entity) use ($permissions) {
            return !is_null($entity) && $permissions->checkOwnableUserAccess($entity, 'view');
        });

        return empty($path) ? null : new EntityPathMessageLine($path);
    }
}
