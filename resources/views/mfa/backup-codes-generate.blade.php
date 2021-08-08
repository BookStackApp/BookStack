@extends('simple-layout')

@section('body')

    <div class="container very-small py-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.mfa_gen_backup_codes_title') }}</h1>
            <p>{{ trans('auth.mfa_gen_backup_codes_desc') }}</p>

            <div class="text-center mb-xs">
                <div class="text-bigger code-base p-m" style="column-count: 2">
                    @foreach($codes as $code)
                        {{ $code }} <br>
                    @endforeach
                </div>
            </div>

            <p class="text-right">
                <a href="{{ $downloadUrl }}" download="backup-codes.txt" class="button outline small">{{ trans('auth.mfa_gen_backup_codes_download') }}</a>
            </p>

            <p class="callout warning">
                {{ trans('auth.mfa_gen_backup_codes_usage_warning') }}
            </p>

            <form action="{{ url('/mfa/backup_codes/confirm') }}" method="POST">
                {{ csrf_field() }}
                <div class="mt-s text-right">
                    <a href="{{ url('/mfa/setup') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button">{{ trans('auth.mfa_gen_confirm_and_enable') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop
