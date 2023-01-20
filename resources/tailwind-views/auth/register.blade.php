@extends('layouts-v2.simple')

@section('content')
    <section class="smf-bg-gray-50 dark:smf-bg-gray-900 smf-flex smf-flex-1 smf-items-center smf-place-content-center">
        <div class="smf-container smf-flex  smf-justify-center smf-px-6 smf-py-16 smf-space-y-2 smf-mx-auto lg:smf-py-16">
            <div
                class="smf-w-screen smf-bg-white smf-rounded-lg smf-shadow dark:smf-border md:smf-mt-0 sm:smf-max-w-md xl:smf-p-0 dark:smf-bg-gray-800 dark:smf-border-gray-700">
                <div class="smf-p-6 smf-space-y-4 sm:smf-p-8">
                    <div>
                        <h1
                            class="smf-text-xl smf-font-bold smf-leading-tight smf-tracking-tight smf-text-gray-900 md:smf-text-2xl dark:smf-text-white">
                            {{ Str::title(trans('auth.sign_up')) }}
                        </h1>
                        @include('auth.parts.register-message')
                    </div>

                    @include('auth.parts.register-form')


                    <div class="smf-flex smf-flex-row smf-items-center smf-place-content-center smf-gap-x-3">
                        <div class="smf-w-24 smf-border-gray-100 smf-border-solid smf-border-b-2"></div>
                        <div
                            class="smf-max-w-2xl smf-font-light smf-text-gray-500 dark:smf-text-gray-400 md:smf-text-xs lg:smf-text-sm">
                            Or continue with
                        </div>
                        <div class="smf-w-24 smf-border-gray-100 smf-border-solid smf-border-b-2"></div>

                    </div>

                    <div class="smf-flex smf-flex-row smf-items-center smf-place-content-center smf-gap-x-3">
                        @if (count($socialDrivers) > 0)
                            @foreach ($socialDrivers as $driver => $name)
                                {{-- <div> --}}
                                <a id="social-register-{{ $driver }}"
                                    href="{{ url('/register/service/' . $driver) }}"
                                    class="smf-inline-flex smf-items-center smf-p-2 smf-m-2 smf-text-sm smf-font-semibold smf-text-gray-800 smf-bg-primary-100 smf-rounded-full dark:smf-bg-primary-700 dark:smf-text-gray-300">
                                    @icon('auth/' . $driver, ['className' => 'smf-w-6 smf-h-6 smf-m-0 smf-fill-primary-600'])
								</a>
                                {{-- </div> --}}
                            @endforeach
                        @endif
                    </div>

                    <div class="smf-flex smf-flex-row smf-items-center smf-place-content-center">
                        <p class="smf-text-sm smf-font-light smf-text-gray-500 dark:smf-text-gray-400">
                            <a href="{{ url('/login') }}"
                                class="smf-font-light hover:smf-underline dark:smf-text-gray-500">{{ trans('auth.already_have_account') }}</a>
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

@stop
