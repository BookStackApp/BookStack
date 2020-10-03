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
                    <th>{{ trans('settings.recycle_bin_deleted_item') }}</th>
                    <th>{{ trans('settings.recycle_bin_deleted_by') }}</th>
                    <th>{{ trans('settings.recycle_bin_deleted_at') }}</th>
                </tr>
                @if(count($deletions) === 0)
                    <tr>
                        <td colspan="3">
                            <p class="text-muted"><em>{{ trans('settings.recycle_bin_contents_empty') }}</em></p>
                        </td>
                    </tr>
                @endif
                @foreach($deletions as $deletion)
                <tr>
                    <td>
                        <div class="table-entity-item mb-m">
                            <span role="presentation" class="icon text-{{$deletion->deletable->getType()}}">@icon($deletion->deletable->getType())</span>
                            <div class="text-{{ $deletion->deletable->getType() }}">
                                {{ $deletion->deletable->name }}
                            </div>
                        </div>
                        @if($deletion->deletable instanceof \BookStack\Entities\Book)
                            <div class="pl-xl block inline">
                                <div class="text-chapter">
                                    @icon('chapter') {{ trans_choice('entities.x_chapters', $deletion->deletable->chapters()->withTrashed()->count()) }}
                                </div>
                            </div>
                        @endif
                        @if($deletion->deletable instanceof \BookStack\Entities\Book || $deletion->deletable instanceof \BookStack\Entities\Chapter)
                        <div class="pl-xl block inline">
                            <div class="text-page">
                                @icon('page') {{ trans_choice('entities.x_pages', $deletion->deletable->pages()->withTrashed()->count()) }}
                            </div>
                        </div>
                        @endif
                    </td>
                    <td>@include('partials.table-user', ['user' => $deletion->deleter, 'user_id' => $deletion->deleted_by])</td>
                    <td>{{ $deletion->created_at }}</td>
                </tr>
                @endforeach
            </table>

            {!! $deletions->links() !!}

        </div>

    </div>
@stop
