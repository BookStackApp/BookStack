<form action="{{ url('/login') }}" method="POST" id="login-form" class="smf-space-y-4 md:smf-space-y-6">
    {!! csrf_field() !!}
    <div>
        <label for="email"
            class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.email') }}</label>
        <input type="email" name="email" id="email"
            class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
            placeholder="" required="">
    </div>
    <div>
        <label for="password"
            class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.password') }}</label>
        <input type="password" name="password" id="password" placeholder=""
            class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
            required="">
    </div>
    <div class="smf-flex smf-items-center smf-justify-between">
        <div class="smf-flex smf-items-start">
            <div class="smf-flex smf-items-center smf-h-5">
                <input id="remember" aria-describedby="remember" type="checkbox"
                    class="smf-w-4 smf-h-4 smf-border smf-border-gray-300 smf-rounded smf-bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:focus:ring-primary-600 dark:smf-ring-offset-gray-800">
            </div>
            <div class="smf-ml-3 smf-text-sm">
                <label for="remember"
                    class="smf-text-gray-500 dark:smf-text-gray-300">{{ trans('auth.remember_me') }}</label>
            </div>
        </div>
        <a href="{{ url('/password/email') }}"
            class="smf-text-sm smf-font-medium text-primary-600 hover:smf-underline dark:text-primary-500">{{ trans('auth.forgot_password') }}</a>
    </div>
    <button type="submit"
        class="smf-w-full smf-text-white smf-bg-primary-600 hover:smf-bg-primary-700 focus:smf-ring-4 focus:smf-outline-none focus:smf-ring-primary-300 smf-font-medium smf-rounded-lg smf-text-sm smf-px-5 smf-py-2.5 smf-text-center dark:smf-bg-primary-600 dark:hover:smf-bg-primary-700 dark:focus:smf-ring-primary-800">{{ Str::title(trans('auth.log_in')) }}</button>



</form>
