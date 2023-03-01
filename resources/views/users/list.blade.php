@extends('layouts.simple')

@section('body')
    <div class="container small pt-xl">
        <main class="card content-wrap">
            <h1 class="list-heading">{{ $title }}</h1>


            <div class="flex-container-row items-center justify-space-between gap-m mt-m mb-l wrap">
                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url('/users/all') }}">
                            <input type="text" name="search" placeholder="{{ trans('settings.users_search') }}"
                                value="{{ $listOptions->getSearch() }}">
                        </form>
                    </div>
                </div>
                <div class="justify-flex-end">
                    @include('common.sort', $listOptions->getSortControlData())
                </div>
            </div>

            <div class="item-list">
                @foreach ($users as $user)
                    @if (!in_array($user->id, $skippedUserIds))
                        @include('users.parts.users-list-item-public', ['user' => $user])
                    @endif
                @endforeach
            </div>
            <div class="text-center">
                {!! $users->links() !!}
            </div>
        </main>
    </div>
@stop
