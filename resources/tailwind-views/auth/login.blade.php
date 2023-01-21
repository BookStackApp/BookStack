@extends('layouts-v2.simple')
@section('content')

    <section class="smf-bg-gray-50 dark:smf-bg-gray-900 smf-flex smf-flex-1 smf-items-center smf-place-content-center">
        <div class="smf-container smf-flex  smf-justify-center smf-px-6 smf-py-16 smf-space-y-2 smf-mx-auto lg:smf-py-16">
            <div
                class="smf-w-screen smf-bg-white smf-rounded-lg smf-shadow dark:smf-border md:smf-mt-0 sm:smf-max-w-md xl:smf-p-0 dark:smf-bg-gray-800 dark:smf-border-gray-700">
                <div class="smf-p-6 smf-space-y-4 md:smf-space-y-6 sm:smf-p-8">
                    <div>
                        <h1
                            class="smf-text-xl smf-font-bold smf-leading-tight smf-tracking-tight smf-text-gray-900 md:smf-text-2xl dark:smf-text-white">
                            {{ Str::title(trans('auth.log_in')) }}
                        </h1>
                        @include('auth.parts.login-message')
                    </div>

                    @include('auth.parts.login-form-' . $authMethod)


                    <div class="smf-flex smf-flex-row smf-items-center smf-place-content-center smf-gap-x-3">
                        <div class="smf-w-8 lg:smf-w-24 smf-border-gray-100 smf-border-solid smf-border-b-2"></div>
                        <div
                            class="smf-max-w-2xl smf-font-light smf-text-gray-500 dark:smf-text-gray-400 md:smf-text-xs lg:smf-text-sm">
                            Or continue with
                        </div>
                        <div class="smf-w-8 lg:smf-w-24 smf-border-gray-100 smf-border-solid smf-border-b-2"></div>

                    </div>

                    <div class="smf-flex smf-flex-row smf-items-center smf-place-content-center smf-gap-x-3">
                        @if (count($socialDrivers) > 0)
                            @foreach ($socialDrivers as $driver => $name)
                                {{-- <div> --}}
                                <a id="social-login-{{ $driver }}" href="{{ url('/login/service/' . $driver) }}"
                                    class="smf-inline-flex smf-items-center smf-p-2 smf-m-2 smf-text-sm smf-font-semibold smf-text-gray-800 smf-bg-primary-100 smf-rounded-full dark:smf-bg-primary-700 dark:smf-text-gray-300">
                                    @icon('auth/' . $driver, ['className' => 'smf-w-6 smf-h-6 smf-m-0 smf-fill-primary-600'])
                                </a>
                                {{-- </div> --}}
                            @endforeach
                        @endif
                    </div>

                    @if (setting('registration-enabled') && config('auth.method') === 'standard')
                        <div class="smf-flex smf-flex-row smf-items-center smf-place-content-center">
                            <p class="smf-text-sm smf-font-light smf-text-gray-500 dark:smf-text-gray-400">
                                 <a href="{{ url('/register') }}"
                                    class="smf-font-medium smf-text-primary-600 hover:smf-underline dark:smf-text-primary-500">{{ trans('auth.dont_have_account') }}</a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@stop
