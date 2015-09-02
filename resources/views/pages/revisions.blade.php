@extends('base')

@section('content')

    <div class="faded-small">
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


    <div class="page-content">
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
                        <td style="line-height: 0;"><img class="avatar" src="{{ $revision->createdBy->getAvatar(30) }}" alt="{{$revision->createdBy->name}}"></td>
                        <td> {{$revision->createdBy->name}}</td>
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
