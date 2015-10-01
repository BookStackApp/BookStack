@extends('base')

@section('content')


    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="page-content anim fadeIn">
                    @include('pages/page-display')
                </div>
            </div>
        </div>
    </div>



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
