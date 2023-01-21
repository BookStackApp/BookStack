@extends('layouts-v2.simple')

@section('content')

    {{-- <div class="container very-small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.reset_password') }}</h1>

            <form action="{{ url("/password/reset") }}" method="POST" class="stretch-inputs">
                {!! csrf_field() !!}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @include('form.text', ['name' => 'email'])
                </div>

                <div class="form-group">
                    <label for="password">{{ trans('auth.password') }}</label>
                    @include('form.password', ['name' => 'password'])
                </div>

                <div class="form-group">
                    <label for="password_confirmation">{{ trans('auth.password_confirm') }}</label>
                    @include('form.password', ['name' => 'password_confirmation'])
                </div>

                <div class="from-group text-right mt-m">
                    <button class="button">{{ trans('auth.reset_password') }}</button>
                </div>
            </form>

        </div>
    </div> --}}

	<section class="smf-bg-gray-50 dark:smf-bg-gray-900 smf-flex smf-flex-1 smf-items-center smf-place-content-center">
        <div class="smf-container smf-flex  smf-justify-center smf-px-6 smf-py-16 smf-space-y-2 smf-mx-auto lg:smf-py-16">
            <div
                class="smf-w-screen smf-bg-white smf-rounded-lg smf-shadow dark:smf-border md:smf-mt-0 sm:smf-max-w-md xl:smf-p-0 dark:smf-bg-gray-800 dark:smf-border-gray-700">
                <div class="smf-p-6 smf-space-y-4 sm:smf-p-8">
                    <div>
                        <h1
                            class="smf-text-xl smf-font-bold smf-leading-tight smf-tracking-tight smf-text-gray-900 md:smf-text-2xl dark:smf-text-white">
                            {{ Str::title(trans('auth.reset_password')) }}
                        </h1>
                        <p
                            class="smf-mb-6 smf-max-w-2xl smf-font-light smf-text-gray-500 dark:smf-text-gray-400 md:smf-text-base lg:smf-mb-8 lg:smf-text-base">
                            {{ trans('auth.reset_password_send_instructions') }}
                        </p>
                    </div>
                    <form action="{{ url('/password/reset') }}" method="POST" id="reset-password"
                        class="smf-space-y-4 md:smf-space-y-6">
                        {!! csrf_field() !!}
                        <div>
							<input type="hidden" name="token" value="{{ $token }}">
                            <label for="email"
                                class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.email') }}</label>
                            <input type="email" name="email" id="email"
                                class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
                                placeholder="" required="">
                        </div>
						<div>
							<label for="password" class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.password') }}</label>
							<input type="password" name="password" id="password" placeholder="" class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500" required="">
						</div>

						<div>
							<label for="password_confirmation" class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.password_confirmation') }}</label>
							<input type="password" name="password_confirmation" id="password_confirmation" placeholder="" class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500" required="">
						</div>

                        <button type="submit"
                            class="smf-w-full smf-text-white smf-bg-primary-600 hover:smf-bg-primary-700 focus:smf-ring-4 focus:smf-outline-none focus:smf-ring-primary-300 smf-font-medium smf-rounded-lg smf-text-sm smf-px-5 smf-py-2.5 smf-text-center dark:smf-bg-primary-600 dark:hover:smf-bg-primary-700 dark:focus:smf-ring-primary-800">{{ Str::title(trans('auth.reset_password')) }}</button>
                    </form>

                </div>
            </div>
        </div>
    </section>


@stop