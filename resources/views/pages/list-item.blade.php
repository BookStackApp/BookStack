<div class="page {{$page->draft ? 'draft' : ''}} entity-list-item" data-entity-type="page" data-entity-id="{{$page->id}}">
    <div class="entity-icon text-page">@icon('page')</div>
    <div class="content">

        <h4>
            @if (isset($showPath) && $showPath)
                <a href="{{ $page->book->getUrl() }}" class="text-book">
                    @icon('book'){{ $page->book->getShortName() }}
                </a>
                <span class="text-muted">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>
                @if($page->chapter)
                    <a href="{{ $page->chapter->getUrl() }}" class="text-chapter">
                        @icon('chapter'){{ $page->chapter->getShortName() }}
                    </a>
                    <span class="text-muted">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>
                @endif
            @endif
            <a href="{{ $page->getUrl() }}" class="entity-list-item-link"><span class="entity-list-item-name break-text">{{ $page->name }}</span></a>
        </h4>


        <div class="entity-item-snippet">
            @if(isset($page->searchSnippet))
                <p class="text-muted break-text">{!! $page->searchSnippet !!}</p>
            @else
                <p class="text-muted break-text">{{ $page->getExcerpt() }}</p>
            @endif
        </div>

        @if(isset($style) && $style === 'detailed')
            <div class="row meta text-muted text-small">
                <div class="col-md-6">
                    @include('partials.entity-meta', ['entity' => $page])
                </div>
                <div class="col-md-6">
                    <a class="text-book" href="{{ $page->book->getUrl() }}">@icon('book'){{ $page->book->getShortName(30) }}</a>
                    <br>
                    @if($page->chapter)
                        <a class="text-chapter" href="{{ $page->chapter->getUrl() }}">@icon('chapter'){{ $page->chapter->getShortName(30) }}</a>
                    @else
                        @icon('chapter') {{ trans('entities.pages_not_in_chapter') }}
                    @endif
                </div>
            </div>
        @endif

    </div>



</div>