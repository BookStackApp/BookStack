@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('pages._breadcrumbs', ['page' => $page])
    </div>
@stop

@section('body')
    <div class="container" ng-non-bindable>
        <p>&nbsp;</p>

        <div class="card">
            <h3>@icon('history') {{ trans('entities.pages_revisions') }}</h3>
            <div class="body">
                @if(count($page->revisions) > 0)

                    <table class="table">
                        <tr>
                            <th width="3%">{{ trans('entities.pages_revisions_number') }}</th>
                            <th width="23%">{{ trans('entities.pages_name') }}</th>
                            <th colspan="2" width="8%">{{ trans('entities.pages_revisions_created_by') }}</th>
                            <th width="15%">{{ trans('entities.pages_revisions_date') }}</th>
                            <th width="25%">{{ trans('entities.pages_revisions_changelog') }}</th>
                            <th width="20%">{{ trans('common.actions') }}</th>
                        </tr>
                        @foreach($page->revisions as $index => $revision)
                            <tr>
                                <td>{{ $revision->revision_number == 0 ? '' : $revision->revision_number }}</td>
                                <td>{{ $revision->name }}</td>
                                <td style="line-height: 0;">
                                    @if($revision->createdBy)
                                        <img class="avatar" src="{{ $revision->createdBy->getAvatar(30) }}" alt="{{ $revision->createdBy->name }}">
                                    @endif
                                </td>
                                <td> @if($revision->createdBy) {{ $revision->createdBy->name }} @else {{ trans('common.deleted_user') }} @endif</td>
                                <td><small>{{ $revision->created_at->format('jS F, Y H:i:s') }} <br> ({{ $revision->created_at->diffForHumans() }})</small></td>
                                <td>{{ $revision->summary }}</td>
                                <td>
                                    <a href="{{ $revision->getUrl('changes') }}" target="_blank">{{ trans('entities.pages_revisions_changes') }}</a>
                                    <span class="text-muted">&nbsp;|&nbsp;</span>

                                    @if ($index === 0)
                                        <a target="_blank" href="{{ $page->getUrl() }}"><i>{{ trans('entities.pages_revisions_current') }}</i></a>
                                    @else
                                        <a href="{{ $revision->getUrl() }}" target="_blank">{{ trans('entities.pages_revisions_preview') }}</a>
                                        <span class="text-muted">&nbsp;|&nbsp;</span>
                                        <a href="{{ $revision->getUrl('restore') }}">{{ trans('entities.pages_revisions_restore') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>

                @else
                    <p>{{ trans('entities.pages_revisions_none') }}</p>
                @endif
            </div>
        </div>

    </div>

@stop
