@extends('simple-layout')

@section('body')
    <div class="container small py-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Setup Multi-Factor Authentication</h1>
            <p class="mb-none">
                Setup multi-factor authentication as an extra layer of security
                for your user account.
            </p>

            <div class="setting-list">
                <div class="grid half gap-xl">
                    <div>
                        <div class="setting-list-label">Mobile App</div>
                        <p class="small">
                            To use multi-factor authentication you'll need a mobile application
                            that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.
                        </p>
                    </div>
                    <div class="pt-m">
                        @if($userMethods->has('totp'))
                            <div class="text-pos">
                                @icon('check-circle')
                                Already configured
                            </div>
                            <a href="{{ url('/mfa/totp-generate') }}" class="button outline small">Reconfigure</a>
                        @else
                            <a href="{{ url('/mfa/totp-generate') }}" class="button outline">Setup</a>
                        @endif
                    </div>
                </div>

                <div class="grid half gap-xl">
                    <div>
                        <div class="setting-list-label">Backup Codes</div>
                        <p class="small">
                            Securely store a set of one-time-use backup codes
                            which you can enter to verify your identity.
                        </p>
                    </div>
                    <div class="pt-m">
                        @if($userMethods->has('backup_codes'))
                            <div class="text-pos">
                                @icon('check-circle')
                                Already configured
                            </div>
                            <a href="{{ url('/mfa/backup-codes-generate') }}" class="button outline small">Reconfigure</a>
                        @else
                            <a href="{{ url('/mfa/backup-codes-generate') }}" class="button outline">Setup</a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
