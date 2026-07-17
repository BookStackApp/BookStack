@extends('layouts.tri')

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($shelf->description, 100, '...') }}">
    @if($shelf->coverInfo()->exists())
        <meta property="og:image" content="{{ $shelf->coverInfo()->getUrl() }}">
    @endif
@endpush

@include('entities.body-tag-classes', ['entity' => $shelf])

@section('body')

    <div class="mb-s print-hidden">
        @include('entities.breadcrumbs', ['crumbs' => [
            $shelf,
        ]])
    </div>

    <main class="card content-wrap">

        <div class="flex-container-row wrap v-center">
            <h1 class="flex fit-content break-text">{{ $shelf->name }}</h1>
            <div class="flex"></div>
            <div class="flex fit-content text-m-right my-m ml-m">
                @include('common.sort', $listOptions->getSortControlData())
            </div>
        </div>

        <div class="book-content">
            <div class="text-muted break-text">{!! $shelf->descriptionInfo()->getHtml() !!}</div>
            @if(count($sortedVisibleShelfBooks) > 0)
                @if($view === 'list')
                    <div class="entity-list">
                        @foreach($sortedVisibleShelfBooks as $book)
                            @include('books.parts.list-item', ['book' => $book])
                        @endforeach
                    </div>
                @else
                    <div class="grid third">
                        @foreach($sortedVisibleShelfBooks as $book)
                            @include('entities.grid-item', ['entity' => $book])
                        @endforeach
                    </div>
                @endif
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mt-xl mb-m">{{ trans('entities.shelves_empty_contents') }}</p>
                    <div class="icon-list inline block">
                        @if(userCan(\BookStack\Permissions\Permission::BookCreateAll) && userCan(\BookStack\Permissions\Permission::BookshelfUpdate, $shelf))
                            <a href="{{ $shelf->getUrl('/create-book') }}" class="icon-list-item text-book">
                                <span class="icon">@icon('add')</span>
                                <span>{{ trans('entities.books_create') }}</span>
                            </a>
                        @endif
                        @if(userCan(\BookStack\Permissions\Permission::BookshelfUpdate, $shelf))
                            <a href="{{ $shelf->getUrl('/edit') }}" class="icon-list-item text-bookshelf">
                                <span class="icon">@icon('edit')</span>
                                <span>{{ trans('entities.shelves_edit_and_assign') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </main>

@stop

@section('left')
    @include('shelves.parts.show-sidebar-section-tags', ['shelf' => $shelf])
    @include('shelves.parts.show-sidebar-section-details', ['shelf' => $shelf])
    @include('shelves.parts.show-sidebar-section-activity', ['activity' => $activity])
@stop

@section('right')
    @include('shelves.parts.show-sidebar-section-actions', ['shelf' => $shelf, 'view' => $view])
@stop




