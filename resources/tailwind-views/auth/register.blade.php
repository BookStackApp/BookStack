@extends('layouts-v2.simple')

@section('content')

<section class="smf-bg-gray-50 dark:smf-bg-gray-900 smf-flex smf-flex-1 smf-items-center smf-place-content-center">
	<div class="smf-container smf-flex  smf-justify-center smf-px-6 smf-py-16 smf-space-y-2 smf-mx-auto lg:smf-py-16">
		<div class="smf-w-screen smf-bg-white smf-rounded-lg smf-shadow dark:smf-border md:smf-mt-0 sm:smf-max-w-md xl:smf-p-0 dark:smf-bg-gray-800 dark:smf-border-gray-700">
			<div class="smf-p-6 smf-space-y-4 md:smf-space-y-6 sm:smf-p-8">
				<div>
					<h1 class="smf-text-xl smf-font-bold smf-leading-tight smf-tracking-tight smf-text-gray-900 md:smf-text-2xl dark:smf-text-white">
						{{ Str::title(trans('auth.sign_up')) }}
					</h1>
					@include('auth.parts.register-message')
				</div>
				
				@include('auth.parts.register-form')
			</div>
			@if(count($socialDrivers) > 0)
                <hr class="my-l">
                @foreach($socialDrivers as $driver => $name)
					{{-- <div> --}}
						<a id="social-register-{{$driver}}" class="button outline svg" href="{{ url("/register/service/" . $driver) }}">
							@icon('auth/' . $driver)
							<span>{{ trans('auth.sign_up_with', ['socialDriver' => $name]) }}</span>
						</a>
					{{-- </div> --}}
                @endforeach
            @endif

		</div>
	</div>
  </section>

@stop
