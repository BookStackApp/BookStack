@extends('base')

@section('sidebar')
    <div class="book-tree">
        <h4><a href="/books/{{$sidebarBookTree['slug']}}"><i class="fa fa-book"></i>{{$sidebarBookTree['name']}}</a></h4>
        @if($sidebarBookTree['hasChildren'])
            @include('pages/sidebar-tree-list', ['pageTree' => $sidebarBookTree['pages']])
        @endif
    </div>
@stop

@section('content')

    <div class="row faded-small">
        <div class="col-md-6 faded">
            <div class="breadcrumbs padded-horizontal">
                <a href="{{$book->getUrl()}}"><i class="fa fa-book"></i>{{ $book->name }}</a>
                @if($breadCrumbs)
                    @foreach($breadCrumbs as $parentPage)
                        <span class="sep">&gt;</span>
                        <a href="{{$parentPage->getUrl()}}">{{ $parentPage->name }}</a>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="col-md-6 faded">
            <div class="action-buttons">
                <a href="{{$page->getUrl() . '/edit'}}" ><i class="fa fa-pencil"></i>Edit this page</a>
                <a href="{{$page->getUrl() . '/create'}}"><i class="fa fa-file-o"></i>Create Sub-page</a>
            </div>
        </div>
    </div>

    <div class="side-nav faded">
        <h4>Page Navigation</h4>
        <ul class="page-nav-list">
        </ul>
    </div>


    <div class="page-content">
        <h1>{{$page->name}}</h1>
        @if(count($page->children) > 0)
            <h4 class="text-muted">Sub-pages</h4>
            <div class="page-list">
                @foreach($page->children as $childPage)
                    <a href="{{ $childPage->getUrl() }}">{{ $childPage->name }}</a>
                @endforeach
            </div>
        @endif
        {!! $page->html !!}
    </div>


    <script>
        $(document).ready(function() {

            // Set up document navigation
            var pageNav = $('.page-nav-list');
            var pageContent = $('.page-content');
            var headers = pageContent.find('h1, h2, h3, h4, h5, h6');
            headers.each(function() {
                var header = $(this);
                var tag = header.prop('tagName');
                var listElem = $('<li></li>').addClass('nav-'+tag);
                var link = $('<a></a>').text(header.text().trim()).attr('href', '#');
                listElem.append(link);
                pageNav.append(listElem);
                link.click(function(e) {
                    e.preventDefault();
                    header.smoothScrollTo();
                })
            });

            // Set up link hooks
            var pageId = {{$page->id}};
            headers.each(function() {
                var text = $(this).text().trim();
                var link = '/link/' + pageId + '#' + encodeURIComponent(text);
                var linkHook = $('<a class="link-hook"><i class="fa fa-link"></i></a>')
                        .attr({"data-content": link, href: link, target: '_blank'});
                linkHook.click(function(e) {
                    e.preventDefault();
                    goToText(text);
                });
                $(this).append(linkHook);
            });

            function goToText(text) {
                $('.page-content').find(':contains("'+text+'")').smoothScrollTo();
            }

            if(window.location.hash) {
                var text = window.location.hash.replace(/\%20/g, ' ').substr(1);
                goToText(text);
            }

            //$('[data-toggle="popover"]').popover()
        });
    </script>
@stop
