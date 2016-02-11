@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-md-6 faded">
                    <div class="breadcrumbs">
                        <a href="{{$page->getUrl()}}" class="text-primary text-button"><i class="zmdi zmdi-arrow-left"></i>Back to page</a>
                    </div>
                </div>
                <div class="col-md-6 faded">
                </div>
            </div>
        </div>
    </div>


    <div class="container small" ng-non-bindable>
        <h1>Page Revisions <span class="subheader">For "{{ $page->name }}"</span></h1>

        @if(count($page->revisions) > 0)

            <table class="table">
                <tr>
                    <th>Name</th>
                    <th colspan="2">Created By</th>
                    <th>Revision Date</th>
                    <th>Actions</th>
                </tr>
                @foreach($page->revisions as $revision)
                    <tr>
                        <td>{{$revision->name}}</td>
                        <td style="line-height: 0;">
                            @if($revision->createdBy)
                                <img class="avatar" src="{{ $revision->createdBy->getAvatar(30) }}" alt="{{$revision->createdBy->name}}">
                            @endif
                        </td>
                        <td> @if($revision->createdBy) {{$revision->createdBy->name}} @else Deleted User @endif</td>
                        <td><small>{{$revision->created_at->format('jS F, Y H:i:s')}} ({{$revision->created_at->diffForHumans()}})</small></td>
                        <td>
                            <a href="{{$revision->getUrl()}}" target="_blank">Preview</a>
                            <span class="text-muted">&nbsp;|&nbsp;</span>
                            <a href="{{$revision->getUrl()}}/restore">Restore</a>
                        </td>
                    </tr>
                @endforeach
            </table>

        @else
            <p>This page has no revisions.</p>
        @endif

    </div>

@stop
