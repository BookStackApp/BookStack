@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-6 faded">
                    <div class="breadcrumbs">
                        <a href="{{$book->getUrl()}}" class="text-book"><i class="zmdi zmdi-book"></i>{{ $book->name }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{$page->chapter->name}}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 faded">
                    <div class="action-buttons">
                        @if($currentUser->can('page-update'))
                            <a href="{{$page->getUrl() . '/revisions'}}" class="text-primary"><i class="zmdi zmdi-replay"></i>Revisions</a>
                            <a href="{{$page->getUrl() . '/edit'}}" class="text-primary" ><i class="zmdi zmdi-edit"></i>Edit</a>
                        @endif
                        @if($currentUser->can('page-delete'))
                            <a href="{{$page->getUrl() . '/delete'}}" class="text-neg"><i class="zmdi zmdi-delete"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('pages/sidebar-tree-list', ['book' => $book])
                <div class="side-nav faded">
                    <h4>Page Navigation</h4>
                    <ul class="page-nav-list">
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <div class="page-content anim fadeIn">
                    @include('pages/page-display')
                    <hr>
                    <p class="text-muted small">
                        Created {{$page->created_at->diffForHumans()}} @if($page->createdBy) by {{$page->createdBy->name}} @endif
                        <br>
                        Last Updated {{$page->updated_at->diffForHumans()}} @if($page->createdBy) by {{$page->updatedBy->name}} @endif
                    </p>
                </div>
            </div>
        </div>
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
                $('.side-nav').fadeIn();
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

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.7/styles/solarized_light.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.7/highlight.min.js"></script>
    <script>
        window.onload = function() {
            var aCodes = document.getElementsByTagName('pre');
            for (var i=0; i < aCodes.length; i++) {
                hljs.highlightBlock(aCodes[i]);
            }
        };
    </script>
@stop
