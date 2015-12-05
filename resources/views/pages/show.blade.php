@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 faded">
                    <div class="breadcrumbs">
                        <a href="{{$book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{$page->chapter->getShortName()}}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 faded">
                    <div class="action-buttons">
                        @if($currentUser->can('page-update'))
                            <a href="{{$page->getUrl() . '/revisions'}}" class="text-primary text-button"><i class="zmdi zmdi-replay"></i>Revisions</a>
                            <a href="{{$page->getUrl() . '/edit'}}" class="text-primary text-button" ><i class="zmdi zmdi-edit"></i>Edit</a>
                        @endif
                        @if($currentUser->can('page-delete'))
                            <a href="{{$page->getUrl() . '/delete'}}" class="text-neg text-button"><i class="zmdi zmdi-delete"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="row">
            <div class="col-md-9 print-full-width">
                <div class="page-content anim fadeIn">

                    <div class="pointer-container" id="pointer">
                        <div class="pointer anim">
                            <i class="zmdi zmdi-link"></i>
                            <input readonly="readonly" type="text" placeholder="url">
                            <button class="button icon" title="Copy Link" data-clipboard-text=""><i class="zmdi zmdi-copy"></i></button>
                        </div>
                    </div>

                    @include('pages/page-display')

                    <hr>

                    <p class="text-muted small">
                        Created {{$page->created_at->diffForHumans()}} @if($page->createdBy) by {{$page->createdBy->name}} @endif
                        <br>
                        Last Updated {{$page->updated_at->diffForHumans()}} @if($page->createdBy) by {{$page->updatedBy->name}} @endif
                    </p>

                </div>
            </div>
            <div class="col-md-3 print-hidden">

                @include('pages/sidebar-tree-list', ['book' => $book, 'sidebarTree' => $sidebarTree])

            </div>
        </div>
    </div>





    <script>
        $(document).ready(function() {


            // Set up pointer
            var $pointer = $('#pointer').detach();
            var pageId = {{$page->id}};
            var isSelection = false;

            $pointer.find('input').click(function(e){$(this).select();e.stopPropagation();});
            new ZeroClipboard( $pointer.find('button').first()[0] );

            $(document.body).find('*').on('click focus', function(e) {
                if(!isSelection) {
                    $pointer.detach();
                }
            });

            $('.page-content [id^="bkmrk"]').on('mouseup keyup', function(e) {
                var selection = window.getSelection();
                if(selection.toString().length === 0) return;
                // Show pointer and set link
                var $elem = $(this);
                var link = window.location.protocol + "//" + window.location.host + '/link/' + pageId + '#' + $elem.attr('id');
                $pointer.find('input').val(link);
                $pointer.find('button').first().attr('data-clipboard-text', link);
                $elem.before($pointer);
                $pointer.show();
                e.stopPropagation();

                isSelection = true;
                setTimeout(function() {
                    isSelection = false;
                }, 100);
            });

            function goToText(text) {
                var idElem = $('.page-content').find('#' + text).first();
                if(idElem.length !== 0) {
                    idElem.smoothScrollTo();
                } else {
                    $('.page-content').find(':contains("'+text+'")').smoothScrollTo();
                }
            }

            if(window.location.hash) {
                var text = window.location.hash.replace(/\%20/g, ' ').substr(1);
                goToText(text);
            }

        });
    </script>

    @include('partials/highlight')
@stop
