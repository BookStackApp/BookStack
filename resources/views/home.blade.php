@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-6 faded">
        <div class="action-buttons text-left">
            <a expand-toggle=".entity-list.compact .entity-item-snippet" class="text-primary text-button">@icon('expand-text'){{ trans('common.toggle_details') }}</a>
        </div>
    </div>
@stop

@section('body')
    <div class="container ch-container" ng-non-bindable>
            @if(count($books) > 0)
                @foreach($books as $indexKey => $book)
                    <div class="col-sm-4 ch-column">
                        <div class="ch-panel">
                            <a class="text-book entity-list-item-link d-block ch-text-book" href="{{$book->getUrl()}}">
                                @icon('book')
                                <span class="entity-list-item-name break-text">{{$book->name}}</span>
                            </a>

                            @foreach($chapters as $index => $chapter)
                                @if ($chapter->book->id === $book->id)
                                    <a class="text-chapter entity-list-item-link d-block" href="{{$chapter->getUrl()}}">
                                        @icon('chapter')
                                        {{ $chapter->name }}
                                    </a>
                                @endif
                            @endforeach

                            @foreach($pages as $page)
                                @if ( ($page->book->id === $book->id) && (!$page->chapter) )
                                <a class="text-page entity-list-item-link d-block" href="{{$page->getUrl()}}">
                                    @icon('book')
                                    {{ $page->name }}
                                </a>
                                @endif
                            @endforeach
                            
                            @foreach($links as $link)
                                @if ( is_object($link->book) && ($link->book->id === $book->id) && (!$link->chapter) )
                                <a class="text-link entity-list-item-link d-block" href="{{$link->link_to}}" target="_blank">
                                    @icon('link')
                                    <span class="text-link-span">
                                        {{ $link->name }}
                                    </span>
                                </a>
                                @endif
                            @endforeach
                        </div>               
                    </div>
                    
                @endforeach
            @endif  
    </div>
    


@stop
