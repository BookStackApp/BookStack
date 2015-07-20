@extends('base')

@section('content')

    <div class="row">
        <div class="page-menu col-md-3">
            <div class="page-nav">
                <h4>Navigation</h4>
                <ul class="page-nav-list"></ul>
            </div>
            <div class="page-actions">
                <h4>Actions</h4>
                <div class="list">
                    <a href="{{$page->getUrl() . '/edit'}}" class="muted"><i class="fa fa-pencil"></i>Edit this page</a>
                    <a href="{{$page->getUrl() . '/create'}}" class="muted"><i class="fa fa-file-o"></i>Create Sub-page</a>
                </div>
            </div>
        </div>

        <div class="page-content right col-md-9">
            <div class="breadcrumbs">
                <a href="{{$book->getUrl()}}"><i class="fa fa-book"></i>{{ $book->name }}</a>
                @if($breadCrumbs)
                    @foreach($breadCrumbs as $parentPage)
                        <span class="sep">&gt;</span>
                        <a href="{{$parentPage->getUrl()}}">{{ $parentPage->name }}</a>
                    @endforeach
                @endif
            </div>
            <h1>{{$page->name}}</h1>
            @if(count($page->pages) > 0)
                <h4 class="text-muted">Sub-pages</h4>
                <div class="page-list">
                    @foreach($page->pages as $childPage)
                        <a href="{{ $childPage->getUrl() }}">{{ $childPage->name }}</a>
                    @endforeach
                </div>
            @endif
            {!! $page->html !!}
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // Set up fixed side menu
            $('.page-menu').affix({
                offset: {
                    top: 10,
                    bottom: function () {
                        return (this.bottom = $('.footer').outerHeight(true))
                    }
                }
            });

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
