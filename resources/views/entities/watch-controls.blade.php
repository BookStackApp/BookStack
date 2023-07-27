<form action="{{ $entity->getUrl('/') }}" method="GET">
{{--    {{ method_field('PUT') }}--}}

    <ul class="dropdown-menu xl-limited anchor-left" style="display: block;">
        <li>
            <button name="level" value="default" class="icon-item">
                <span class="text-pos pt-m">{!!  request()->query('level') === 'default' ? icon('check-circle') : '' !!}</span>
                <div class="break-text">
                    <div class="mb-xxs"><strong>Default Preferences</strong></div>
                    <div class="text-muted text-small">
                        Revert watching to just your default notification preferences.
                    </div>
                </div>
            </button>
        </li>
        <li><hr class="my-none"></li>
        <li>
            <button name="level" value="ignore" class="icon-item">
                <span class="text-pos pt-m">{!!  request()->query('level') === 'ignore' ? icon('check-circle') : '' !!}</span>
                <div class="break-text">
                    <div class="mb-xxs"><strong>Ignore</strong></div>
                    <div class="text-muted text-small">
                        Ignore all notifications, including those from user-level preferences.
                    </div>
                </div>
            </button>
        </li>
        <li><hr class="my-none"></li>
        <li>
            <button name="level" value="new" class="icon-item">
                <span class="text-pos pt-m">{!!  request()->query('level') === 'new' ? icon('check-circle') : '' !!}</span>
                <div class="break-text">
                    <div class="mb-xxs"><strong>New Pages</strong></div>
                    <div class="text-muted text-small">
                        Notify when any new page is created within this item.
                    </div>
                </div>
            </button>
        </li>
        <li><hr class="my-none"></li>
        <li>
            <button name="level" value="updates" class="icon-item">
                <span class="text-pos pt-m">{!!  request()->query('level') === 'updates' ? icon('check-circle') : '' !!}</span>
                <div class="break-text">
                    <div class="mb-xxs"><strong>All Page Updates</strong></div>
                    <div class="text-muted text-small">
                        Notify upon all new pages and page changes.
                    </div>
                </div>
            </button>
        </li>
        <li><hr class="my-none"></li>
        <li>
            <button name="level" value="comments" class="icon-item">
                <span class="text-pos pt-m">{!!  request()->query('level') === 'comments' ? icon('check-circle') : '' !!}</span>
                <div class="break-text">
                    <div class="mb-xxs"><strong>All Page Updates & Comments</strong></div>
                    <div class="text-muted text-small">
                        Notify upon all new pages, page changes and new comments.
                    </div>
                </div>
            </button>
        </li>
        <li><hr class="my-none"></li>
        <li>
            <div class="text-small text-muted px-l pb-xxs pt-xs">
                <a href="{{ url('/preferences/notifications') }}" target="_blank">Change default notification preferences</a>
            </div>
        </li>
    </ul>
</form>