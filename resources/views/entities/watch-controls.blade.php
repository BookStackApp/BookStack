<div component="dropdown"
     class="dropdown-container block my-xxs">
    <a refs="dropdown@toggle" href="#" class="entity-meta-item my-none">
        @icon(($ignoring ? 'watch-ignore' : 'watch'))
        <span>{{ $label }}</span>
    </a>
    <form action="{{ url('/watching/update') }}" method="POST">
        {{ method_field('PUT') }}
        {{ csrf_field() }}
        <input type="hidden" name="type" value="{{ get_class($entity) }}">
        <input type="hidden" name="id" value="{{ $entity->id }}">

        <ul refs="dropdown@menu" class="dropdown-menu xl-limited anchor-left pb-none">
            @foreach(\BookStack\Activity\WatchLevels::all() as $option => $value)
                <li>
                    <button name="level" value="{{ $option }}" class="icon-item">
                        @if($watchLevel === $option)
                            <span class="text-pos pt-m"
                                  title="{{ trans('common.status_active') }}">@icon('check-circle')</span>
                        @else
                            <span title="{{ trans('common.status_inactive') }}"></span>
                        @endif
                        <div class="break-text">
                            <div class="mb-xxs"><strong>{{ trans('entities.watch_title_' . $option) }}</strong></div>
                            <div class="text-muted text-small">
                                {{ trans('entities.watch_desc_' . $option) }}
                            </div>
                        </div>
                    </button>
                </li>
                <li>
                    <hr class="my-none">
                </li>
            @endforeach
            <li>
                <a href="{{ url('/preferences/notifications') }}"
                   target="_blank"
                   class="text-item text-muted text-small break-text">{{ trans('entities.watch_change_default') }}</a>
            </li>
        </ul>
    </form>
</div>