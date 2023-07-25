@extends('layouts.simple')

@section('body')
    <div class="container small my-xl">

        <section class="card content-wrap auto-height">
            <form action="{{ url('/preferences/notifications') }}" method="post">
                {{ method_field('put') }}
                {{ csrf_field() }}

                <h1 class="list-heading">{{ trans('preferences.notifications') }}</h1>
                <p class="text-small text-muted">{{ trans('preferences.notifications_desc') }}</p>

                <div class="toggle-switch-list">
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

                <div class="form-group text-right">
                    <button class="button">{{ trans('preferences.notifications_save') }}</button>
                </div>
            </form>
        </section>

    </div>
@stop
