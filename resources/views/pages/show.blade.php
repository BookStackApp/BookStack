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
                <a href="{{$page->getUrl() . '/edit'}}" class="muted"><i class="fa fa-pencil"></i>Edit this page</a>
            </div>
        </div>

        <div class="page-content right col-md-9">
            <h1>{{$page->name}}</h1>
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
            var sortedHeaders = [];
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
