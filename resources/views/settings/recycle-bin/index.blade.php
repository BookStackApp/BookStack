@extends('simple-layout')

@section('body')
    <div class="container">

        <div class="grid left-focus v-center no-row-gap">
            <div class="py-m">
                @include('settings.navbar', ['selected' => 'maintenance'])
            </div>
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.recycle_bin') }}</h2>

            <div class="grid half left-focus">
                <div>
                    <p class="text-muted">{{ trans('settings.recycle_bin_desc') }}</p>
                </div>
                <div class="text-right">
                    <div component="dropdown" class="dropdown-container">
                        <button refs="dropdown@toggle"
                                type="button"
                                class="button outline">{{ trans('settings.recycle_bin_empty') }} </button>
                        <div refs="dropdown@menu" class="dropdown-menu">
                            <p class="text-neg small px-m mb-xs">{{ trans('settings.recycle_bin_empty_confirm') }}</p>

                            <form action="{{ url('/settings/recycle-bin/empty') }}" method="POST">
                                {!! csrf_field() !!}
                                <button type="submit" class="text-primary small delete">{{ trans('common.confirm') }}</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>


            <hr class="mt-l mb-s">

            {!! $deletions->links() !!}

            <table class="table">
                <tr>
                    <th width="50%">{{ trans('settings.recycle_bin_deleted_item') }}</th>
                    <th width="20%">{{ trans('settings.recycle_bin_deleted_by') }}</th>
                    <th width="15%">{{ trans('settings.recycle_bin_deleted_at') }}</th>
                    <th width="15%"></th>
                </tr>
                @if(count($deletions) === 0)
                    <tr>
                        <td colspan="4">
                            <p class="text-muted"><em>{{ trans('settings.recycle_bin_contents_empty') }}</em></p>
                        </td>
                    </tr>
                @endif
                @foreach($deletions as $deletion)
                <tr>
                    <td>
                        <div class="table-entity-item">
                            <span role="presentation" class="icon text-{{$deletion->deletable->getType()}}">@icon($deletion->deletable->getType())</span>
                            <div class="text-{{ $deletion->deletable->getType() }}">
                                {{ $deletion->deletable->name }}
                            </div>
                        </div>
                        @if($deletion->deletable instanceof \BookStack\Entities\Models\Book || $deletion->deletable instanceof \BookStack\Entities\Models\Chapter)
                            <div class="mb-m"></div>
                        @endif
                        @if($deletion->deletable instanceof \BookStack\Entities\Models\Book)
                            <div class="pl-xl block inline">
                                <div class="text-chapter">
                                    @icon('chapter') {{ trans_choice('entities.x_chapters', $deletion->deletable->chapters()->withTrashed()->count()) }}
                                </div>
                            </div>
                        @endif
                        @if($deletion->deletable instanceof \BookStack\Entities\Models\Book || $deletion->deletable instanceof \BookStack\Entities\Models\Chapter)
                        <div class="pl-xl block inline">
                            <div class="text-page">
                                @icon('page') {{ trans_choice('entities.x_pages', $deletion->deletable->pages()->withTrashed()->count()) }}
                            </div>
                        </div>
                        @endif
                    </td>
                    <td>@include('partials.table-user', ['user' => $deletion->deleter, 'user_id' => $deletion->deleted_by])</td>
                    <td width="200">{{ $deletion->created_at }}</td>
                    <td width="150" class="text-right">
                        <div component="dropdown" class="dropdown-container">
                            <button type="button" refs="dropdown@toggle" class="button outline">{{ trans('common.actions') }}</button>
                            <ul refs="dropdown@menu" class="dropdown-menu">
                                <li><a class="block" href="{{ url('/settings/recycle-bin/'.$deletion->id.'/restore') }}">{{ trans('settings.recycle_bin_restore') }}</a></li>
                                <li><a class="block" href="{{ url('/settings/recycle-bin/'.$deletion->id.'/destroy') }}">{{ trans('settings.recycle_bin_permanently_delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </table>

            {!! $deletions->links() !!}

        </div>

    </div>
@stop
