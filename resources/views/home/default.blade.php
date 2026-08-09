@extends('layouts.simple')

@section('body')

    <div class="container px-xl py-s flex-container-row gap-l wrap justify-space-between">
        <div class="icon-list inline block">
            @include('home.parts.expand-toggle', ['classes' => 'text-muted text-link', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
        </div>
        <div>
            <div class="icon-list inline block">
                @include('common.dark-mode-toggle', ['classes' => 'text-muted icon-list-item text-link'])
            </div>
        </div>
    </div>

    <div class="container" id="home-default">
        <div class="grid third gap-x-xxl no-row-gap">
            <div>
                @include('home.parts.default-card-recent-drafts', ['draftPages' => $draftPages])
                @include('home.parts.default-card-recently-viewed-or-recent-books', ['recents' => $recents])
            </div>

            <div>
                @include('home.parts.default-card-top-favourites', ['favourites' => $favourites])
                @include('home.parts.default-card-recently-updates-pages', ['recentlyUpdatedPages' => $recentlyUpdatedPages])
            </div>

            <div>
                @include('home.parts.default-card-recent-activity', ['activity' => $activity])
            </div>
        </div>
    </div>

@stop
