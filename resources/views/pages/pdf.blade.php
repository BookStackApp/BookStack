<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $page->name }}</title>

    <style>
        {!! $css !!}
    </style>
</head>
<body>
<div class="container" id="page-show" ng-non-bindable>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-content">

                @include('pages/page-display')

                <hr>

                <p class="text-muted small">
                    Created {{$page->created_at->diffForHumans()}} @if($page->createdBy) by {{$page->createdBy->name}} @endif
                    <br>
                    Last Updated {{$page->updated_at->diffForHumans()}} @if($page->updatedBy) by {{$page->updatedBy->name}} @endif
                </p>

            </div>
        </div>
    </div>
</div>
</body>
</html>
