@extends('layouts.simple')

@section('body')

    <div class="container medium pt-xl">

        <div class="grid right-focus reverse-collapse">

            <div>
                <section id="recent-user-activity" class="mb-xl">
                    <h5>{{ trans('entities.recent_activity') }}</h5>
                    @include('common.activity-list', ['activity' => $activity])
                </section>
            </div>

            <div>
                <section class="card content-wrap auto-height">
                    <div class="grid half v-center">
                        <div>
                            <div class="mr-m float left">
                                <img class="avatar square huge" src="{{ $user->getAvatar(120) }}" alt="{{ $user->name }}">
                            </div>
                            <div>
                                <h4 class="mt-md">{{ $user->name }}</h4>
                                <p class="text-muted">
                                    {{ trans('entities.profile_user_for_x', ['time' => $user->created_at->diffForHumans(null, true)]) }}
                                </p>
                            </div>
                        </div>
                        <div id="content-counts">
                            <div class="text-muted">{{ trans('entities.profile_created_content') }}</div>
                            <div class="grid half v-center no-row-gap">
                                <div class="icon-list">
                                    <span class="text-page draft icon-list-item">
                                        <span>@icon('star')</span>
                                        <span>{{ trans_choice('entities.x_symbols', $user->asset_counts['symbols']) }}</span>
                                    </span>
                                </div>
                                <div class="icon-list">
                                    <span class="text-chapter icon-list-item">
                                        <span>@icon('file')</span>
                                        <span>{{ trans_choice('entities.x_drafts', $user->asset_counts['drafts']) }}</span>
                                    </span>
                                </div>
                                <div class="icon-list">
                                    <span class="text-book icon-list-item">
                                        <span>@icon('edit')</span>
                                        <span>{{ trans_choice('entities.x_updates', $user->asset_counts['updates']) }}</span>
                                    </span>
                                </div>
                                <div class="icon-list">
                                    <a href="{{ url('/search?term=' . urlencode('{created_by:' . $user->slug . '} {type:page}')) }}" class="text-page icon-list-item">
                                        <span>@icon('page')</span>
                                        <span>All Created</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

                <section class="card content-wrap auto-height book-contents">
                    <h2 id="recent-pages" class="list-heading">
                        Recently Created Symbols
                    </h2>
                    @if (count($recentlyCreated['symbols']) > 0)
                        @include('entities.list', [
                            'entities' => $recentlyCreated['symbols'],
                            'showPath' => true,
                        ])
                    @else
                        <p class="text-muted">
                            {{ trans('entities.profile_not_created_symbols', ['userName' => $user->name]) }}</p>
                    @endif
                </section>

                <section class="card content-wrap auto-height book-contents">
                    <h2 id="recent-chapters" class="list-heading">
                        Recent Created Drafts
                    </h2>
                    @if (count($recentlyCreated['drafts']) > 0)
                        @include('entities.list', [
                            'entities' => $recentlyCreated['drafts'],
                            'showPath' => true,
                        ])
                    @else
                        <p class="text-muted">
                            {{ trans('entities.profile_not_created_drafts', ['userName' => $user->name]) }}</p>
                    @endif
                </section>

                <section class="card content-wrap auto-height book-contents">
                    <h2 id="recent-chapters" class="list-heading">
                        Recent Edits
                    </h2>
                    @if (count($recentlyCreated['updates']) > 0)
                        @include('entities.list', [
                            'entities' => $recentlyCreated['updates'],
                            'showPath' => true,
                        ])
                    @else
                        <p class="text-muted">
                            {{ trans('entities.profile_not_updated', ['userName' => $user->name]) }}</p>
                    @endif
                </section>
            </div>

        </div>


    </div>
@stop