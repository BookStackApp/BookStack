@extends('tri-layout')

@section('body')

    <div class="mb-s">
        @include('partials.breadcrumbs', ['crumbs' => [
            $shelf,
        ]])
    </div>

    <div class="card content-wrap">
        <h1 class="break-text">{{$shelf->name}}</h1>
        <div class="book-content">
            <p class="text-muted">{!! nl2br(e($shelf->description)) !!}</p>
            @if(count($books) > 0)
                <div class="entity-list">
                    @foreach($books as $book)
                        @include('books/list-item', ['book' => $book])
                    @endforeach
                </div>
            @else
                <p>
                    <hr>
                    <span class="text-muted italic">{{ trans('entities.shelves_empty_contents') }}</span>
                    @if(userCan('bookshelf-create', $shelf))
                        <br/>
                        <a href="{{ $shelf->getUrl('/edit') }}" class="button outline bookshelf">{{ trans('entities.shelves_edit_and_assign') }}</a>
                    @endif
                </p>
            @endif
        </div>
    </div>

@stop

@section('left')

    @if($shelf->tags->count() > 0)
        <div id="tags" class="mb-xl">
            @include('components.tag-list', ['entity' => $shelf])
        </div>
    @endif

    <div id="details" class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="text-small text-muted blended-links">
            @include('partials.entity-meta', ['entity' => $shelf])
            @if($shelf->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $shelf))
                        <a href="{{ $shelf->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.shelves_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.shelves_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(count($activity) > 0)
        <div class="mb-xl">
            <h5>{{ trans('entities.recent_activity') }}</h5>
            @include('partials.activity-list', ['activity' => $activity])
        </div>
    @endif
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            @if(userCan('bookshelf-update', $shelf))
                <a href="{{ $shelf->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif

            @if(userCan('restrictions-manage', $shelf))
                <a href="{{ $shelf->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif

            @if(userCan('bookshelf-delete', $shelf))
                <a href="{{ $shelf->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

        </div>
    </div>
@stop




