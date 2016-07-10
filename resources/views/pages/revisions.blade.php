@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{$page->book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $page->book->getShortName() }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{$page->chapter->getShortName()}}
                            </a>
                        @endif
                        <span class="sep">&raquo;</span>
                        <a href="{{$page->getUrl()}}" class="text-page text-button"><i class="zmdi zmdi-file"></i>{{ $page->getShortName() }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container" ng-non-bindable>
        <h1>Page Revisions <span class="subheader">For "{{ $page->name }}"</span></h1>

        @if(count($page->revisions) > 0)

            <table class="table">
                <tr>
                    <th width="25%">Name</th>
                    <th colspan="2" width="10%">Created By</th>
                    <th width="15%">Revision Date</th>
                    <th width="25%">Changelog</th>
                    <th width="15%">Actions</th>
                </tr>
                @foreach($page->revisions as $index => $revision)
                    <tr>
                        <td>{{$revision->name}}</td>
                        <td style="line-height: 0;">
                            @if($revision->createdBy)
                                <img class="avatar" src="{{ $revision->createdBy->getAvatar(30) }}" alt="{{$revision->createdBy->name}}">
                            @endif
                        </td>
                        <td> @if($revision->createdBy) {{$revision->createdBy->name}} @else Deleted User @endif</td>
                        <td><small>{{$revision->created_at->format('jS F, Y H:i:s')}} <br> ({{$revision->created_at->diffForHumans()}})</small></td>
                        <td>{{$revision->summary}}</td>
                        @if ($index !== 0)
                            <td>
                                <a href="{{$revision->getUrl()}}" target="_blank">Preview</a>
                                <span class="text-muted">&nbsp;|&nbsp;</span>
                                <a href="{{$revision->getUrl()}}/restore">Restore</a>
                            </td>
                        @else
                            <td><a target="_blank" href="{{ $page->getUrl() }}"><i>Current Version</i></a></td>
                        @endif
                    </tr>
                @endforeach
            </table>

        @else
            <p>This page has no revisions.</p>
        @endif

    </div>

@stop
