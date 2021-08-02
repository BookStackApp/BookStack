@extends('simple-layout')

@section('body')

    <div class="container very-small py-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Backup Codes</h1>
            <p>
                Store the below list of codes in a safe place.
                When accessing the system you'll be able to use one of the codes
                as a second authentication mechanism.
            </p>

            <div class="text-center mb-xs">
                <div class="text-bigger code-base p-m" style="column-count: 2">
                    @foreach($codes as $code)
                        {{ $code }} <br>
                    @endforeach
                </div>
            </div>

            <p class="text-right">
                <a href="{{ $downloadUrl }}" download="backup-codes.txt" class="button outline small">Download Codes</a>
            </p>

            <p class="callout warning">
                Each code can only be used once
            </p>

            <form action="{{ url('/mfa/backup_codes/confirm') }}" method="POST">
                {{ csrf_field() }}
                <div class="mt-s text-right">
                    <a href="{{ url('/mfa/setup') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button">Confirm and Enable</button>
                </div>
            </form>
        </div>
    </div>

@stop
