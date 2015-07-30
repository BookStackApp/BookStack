@extends('base')

@section('content')

    <div class="row faded-small">
        <div class="col-md-6 faded">
            <div class="breadcrumbs padded-horizontal">
                <a href="{{$book->getUrl()}}"><i class="zmdi zmdi-book"></i>{{ $book->name }}</a>
                @if($page->hasChapter())
                    <span class="sep">&raquo;</span>
                    <a href="{{ $page->chapter->getUrl() }}">
                        <i class="zmdi zmdi-collection-bookmark"></i>
                        {{$page->chapter->name}}
                    </a>
                @endif
            </div>
        </div>
        <div class="col-md-6 faded">
            <div class="action-buttons">
                <a href="{{$page->getUrl() . '/edit'}}" class="text-primary" ><i class="zmdi zmdi-edit"></i>Edit</a>
                <a href="{{$page->getUrl() . '/delete'}}" class="text-neg"><i class="zmdi zmdi-delete"></i>Delete</a>
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
            if(headers.length > 5) {
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
            } else {
                $('.side-nav').hide();
            }


            // Set up link hooks
            var pageId = {{$page->id}};
            headers.each(function() {
                var text = $(this).text().trim();
                var link = '/link/' + pageId + '#' + encodeURIComponent(text);
                var linkHook = $('<a class="link-hook"><i class="zmdi zmdi-link"></i></a>')
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
