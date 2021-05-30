@extends('simple-layout')

@section('body')
    <div class="container">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/revisions') => [
                    'text' => trans('entities.pages_revisions'),
                    'icon' => 'history',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.pages_revisions') }}</h1>
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
                            <td><small>{{ $revision->created_at->formatLocalized('%e %B %Y %H:%M:%S') }} <br> ({{ $revision->created_at->diffForHumans() }})</small></td>
                            <td>{{ $revision->summary }}</td>
                            <td class="actions">
                                <a href="{{ $revision->getUrl('changes') }}" target="_blank" rel="noopener">{{ trans('entities.pages_revisions_changes') }}</a>
                                <span class="text-muted">&nbsp;|&nbsp;</span>


                                @if ($index === 0)
                                    <a target="_blank" rel="noopener" href="{{ $page->getUrl() }}"><i>{{ trans('entities.pages_revisions_current') }}</i></a>
                                @else
                                    <a href="{{ $revision->getUrl() }}" target="_blank" rel="noopener">{{ trans('entities.pages_revisions_preview') }}</a>
                                    <span class="text-muted">&nbsp;|&nbsp;</span>
                                    <div component="dropdown" class="dropdown-container">
                                        <a refs="dropdown@toggle" href="#" aria-haspopup="true" aria-expanded="false">{{ trans('entities.pages_revisions_restore') }}</a>
                                        <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                                            <li class="px-m py-s"><small class="text-muted">{{trans('entities.revision_restore_confirm')}}</small></li>
                                            <li>
                                                <form action="{{ $revision->getUrl('/restore') }}" method="POST">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <button type="submit" class="text-button text-primary">@icon('history'){{ trans('entities.pages_revisions_restore') }}</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    <span class="text-muted">&nbsp;|&nbsp;</span>
                                    <div component="dropdown" class="dropdown-container">
                                        <a refs="dropdown@toggle" href="#" aria-haspopup="true" aria-expanded="false">{{ trans('common.delete') }}</a>
                                        <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                                            <li class="px-m py-s"><small class="text-muted">{{trans('entities.revision_delete_confirm')}}</small></li>
                                            <li>
                                                <form action="{{ $revision->getUrl('/delete/') }}" method="POST">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="text-button text-neg">@icon('delete'){{ trans('common.delete') }}</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>

            @else
                <p>{{ trans('entities.pages_revisions_none') }}</p>
            @endif
        </main>

    </div>

@stop
