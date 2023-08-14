@extends('layouts.simple')

@section('body')
    <div class="container small my-xl">

        <section class="card content-wrap auto-height">
            <form action="{{ url('/preferences/notifications') }}" method="post">
                {{ method_field('put') }}
                {{ csrf_field() }}

                <h1 class="list-heading">{{ trans('preferences.notifications') }}</h1>
                <p class="text-small text-muted">{{ trans('preferences.notifications_desc') }}</p>

                <div class="flex-container-row wrap justify-space-between pb-m">
                    <div class="toggle-switch-list min-width-l">
                        <div>
                            @include('form.toggle-switch', [
                                'name' => 'preferences[own-page-changes]',
                                'value' => $preferences->notifyOnOwnPageChanges(),
                                'label' => trans('preferences.notifications_opt_own_page_changes'),
                            ])
                        </div>
                        <div>
                            @include('form.toggle-switch', [
                                'name' => 'preferences[own-page-comments]',
                                'value' => $preferences->notifyOnOwnPageComments(),
                                'label' => trans('preferences.notifications_opt_own_page_comments'),
                            ])
                        </div>
                        <div>
                            @include('form.toggle-switch', [
                                'name' => 'preferences[comment-replies]',
                                'value' => $preferences->notifyOnCommentReplies(),
                                'label' => trans('preferences.notifications_opt_comment_replies'),
                            ])
                        </div>
                    </div>

                    <div class="mt-auto">
                        <button class="button">{{ trans('preferences.notifications_save') }}</button>
                    </div>
                </div>

            </form>
        </section>

        <section class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('preferences.notifications_watched') }}</h2>
            <p class="text-small text-muted">{{ trans('preferences.notifications_watched_desc') }}</p>

            @if($watches->isEmpty())
                <p class="text-muted italic">{{ trans('common.no_items') }}</p>
            @else
                <div class="item-list">
                    @foreach($watches as $watch)
                        <div class="flex-container-row justify-space-between item-list-row items-center wrap px-m py-s">
                            <div class="py-xs px-s min-width-m">
                                @include('entities.icon-link', ['entity' => $watch->watchable])
                            </div>
                            <div class="py-xs min-width-m text-m-right px-m">
                                @icon('watch' . ($watch->ignoring() ? '-ignore' : ''))
                                {{ trans('entities.watch_title_' . $watch->getLevelName()) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="my-m">{{ $watches->links() }}</div>
        </section>

    </div>
@stop
