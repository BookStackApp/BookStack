@extends('base')


@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 faded">
                    <div class="action-buttons">
                        <a href="/users/{{$user->id}}/delete" class="text-neg text-button"><i class="zmdi zmdi-delete"></i>Delete user</a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container small">

        <div class="row">
            <div class="col-md-6">
                <h1>Edit {{ $user->id === $currentUser->id ? 'Profile' : 'User' }}</h1>
                <form action="/users/{{$user->id}}" method="post">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="put">
                    @include('users/form', ['model' => $user])
                </form>
            </div>
            <div class="col-md-6">
                <h1>&nbsp;</h1>
                <div class="shaded padded margin-top">
                    <p>
                        <img class="avatar" src="{{ $user->getAvatar(80) }}" alt="{{ $user->name }}">
                    </p>
                    <p class="text-muted">You can change your profile picture at <a href="http://en.gravatar.com/">Gravatar</a>.</p>
                </div>
            </div>
        </div>

        <hr class="margin-top large">

        @if($currentUser->id === $user->id && count($activeSocialDrivers) > 0)
            <h3>Social Accounts</h3>
            <p class="text-muted">
                Here you can connect your other accounts for quicker and easier login. <br>
                Disconnecting an account here does not previously authorized access. Revoke access from your profile settings on the connected social account.
            </p>
            <div class="row">
                @if(isset($activeSocialDrivers['google']))
                    <div class="col-md-3 text-center">
                        <div><i class="zmdi zmdi-google-plus-box zmdi-hc-4x" style="color: #DC4E41;"></i></div>
                        <div>
                            @if($user->hasSocialAccount('google'))
                                <a href="/login/service/google/detach" class="button neg">Disconnect Account</a>
                            @else
                                <a href="/login/service/google" class="button pos">Attach Account</a>
                            @endif
                        </div>
                    </div>
                @endif
                @if(isset($activeSocialDrivers['github']))
                    <div class="col-md-3 text-center">
                        <div><i class="zmdi zmdi-github zmdi-hc-4x" style="color: #444;"></i></div>
                        <div>
                            @if($user->hasSocialAccount('github'))
                                <a href="/login/service/github/detach" class="button neg">Disconnect Account</a>
                            @else
                                <a href="/login/service/github" class="button pos">Attach Account</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif


    </div>

    <p class="margin-top large"><br></p>

@stop
