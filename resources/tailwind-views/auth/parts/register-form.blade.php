<form action="{{ url('/register') }}" method="POST" id="register-form" class="smf-space-y-4 md:smf-space-y-6">
    {!! csrf_field() !!}
    <div>
        {{-- <label for="name" class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.email') }}</label> --}}
        <input type="name" name="name" id="name"
            class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
            placeholder="Name" required="true">
    </div>
    <div>
        {{-- <label for="email" class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.name') }}</label> --}}
        <input type="email" name="email" id="email"
            class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
            placeholder="Email" required="true">
    </div>
		<div>
			{{-- <label for="email" class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.name') }}</label> --}}
			<input type="number" name="mobile" id="mobile"
					class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
					placeholder="Mobile" required="false">
	</div>
    <div>
        {{-- <label for="password" class="smf-block smf-mb-2 smf-text-sm smf-font-medium smf-text-gray-900 dark:smf-text-white">{{ trans('auth.password') }}</label> --}}
        <input type="password" name="password" id="password" placeholder="Password"
            class="smf-bg-gray-50 smf-border smf-border-gray-300 smf-text-gray-900 sm:smf-text-sm smf-rounded-lg focus:ring-primary-600 focus:border-primary-600 smf-block smf-w-full smf-p-2.5 dark:smf-bg-gray-700 dark:smf-border-gray-600 dark:smf-placeholder-gray-400 dark:smf-text-white dark:focus:smf-ring-blue-500 dark:focus:smf-border-blue-500"
            required="true">
    </div>

	<p class="smf-mb-6 smf-max-w-2xl smf-font-light smf-text-gray-500 dark:smf-text-gray-400 md:smf-text-xs lg:smf-mb-8 lg:smf-text-sm">
		By creating an account, I agree to the <a>Terms</a> and <a>Privacy Policy.</a>
	</p>

    <button type="submit"
        class="smf-w-full smf-text-white smf-bg-primary-600 hover:smf-bg-primary-700 focus:smf-ring-4 focus:smf-outline-none focus:smf-ring-primary-300 smf-font-medium smf-rounded-lg smf-text-sm smf-px-5 smf-py-2.5 smf-text-center dark:smf-bg-primary-600 dark:hover:smf-bg-primary-700 dark:focus:smf-ring-primary-800">{{ Str::title(trans('auth.create_account')) }}</button>



</form>
