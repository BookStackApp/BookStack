<?php

return [

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Enable Clockwork
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork is enabled by default only when your application is in debug mode. Here you can explicitly enable or
	| disable Clockwork. When disabled, no data is collected and the api and web ui are inactive.
	|
	*/

	'enable' => env('CLOCKWORK_ENABLE', false),

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Features
	|------------------------------------------------------------------------------------------------------------------
	|
	| You can enable or disable various Clockwork features here. Some features have additional settings (eg. slow query
	| threshold for database queries).
	|
	*/

	'features' => [

		// Cache usage stats and cache queries including results
		'cache' => [
			'enabled' => true,

			// Collect cache queries
			'collect_queries' => true,

			// Collect values from cache queries (high performance impact with a very high number of queries)
			'collect_values' => false
		],

		// Database usage stats and queries
		'database' => [
			'enabled' => true,

			// Collect database queries (high performance impact with a very high number of queries)
			'collect_queries' => true,

			// Collect details of models updates (high performance impact with a lot of model updates)
			'collect_models_actions' => true,

			// Collect details of retrieved models (very high performance impact with a lot of models retrieved)
			'collect_models_retrieved' => false,

			// Query execution time threshold in miliseconds after which the query will be marked as slow
			'slow_threshold' => null,

			// Collect only slow database queries
			'slow_only' => false,

			// Detect and report duplicate (N+1) queries
			'detect_duplicate_queries' => false
		],

		// Dispatched events
		'events' => [
			'enabled' => true,

			// Ignored events (framework events are ignored by default)
			'ignored_events' => [
				// App\Events\UserRegistered::class,
				// 'user.registered'
			],
		],

		// Laravel log (you can still log directly to Clockwork with laravel log disabled)
		'log' => [
			'enabled' => true
		],

		// Sent notifications
		'notifications' => [
			'enabled' => true,
		],

		// Performance metrics
		'performance' => [
			// Allow collecting of client metrics. Requires separate clockwork-browser npm package.
			'client_metrics' => true
		],

		// Dispatched queue jobs
		'queue' => [
			'enabled' => true
		],

		// Redis commands
		'redis' => [
			'enabled' => true
		],

		// Routes list
		'routes' => [
			'enabled' => false,

			// Collect only routes from particular namespaces (only application routes by default)
			'only_namespaces' => [ 'App' ]
		],

		// Rendered views
		'views' => [
			'enabled' => true,

			// Collect views including view data (high performance impact with a high number of views)
			'collect_data' => false,

			// Use Twig profiler instead of Laravel events for apps using laravel-twigbridge (more precise, but does
			// not support collecting view data)
			'use_twig_profiler' => false
		]

	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Enable web UI
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork comes with a web UI accessibla via http://your.app/clockwork. Here you can enable or disable this
	| feature. You can also set a custom path for the web UI.
	|
	*/

	'web' => true,

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Enable toolbar
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork can show a toolbar with basic metrics on all responses. Here you can enable or disable this feature.
	| Requires a separate clockwork-browser npm library.
	| For installation instructions see https://underground.works/clockwork/#docs-viewing-data
	|
	*/

	'toolbar' => true,

	/*
	|------------------------------------------------------------------------------------------------------------------
	| HTTP requests collection
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork collects data about HTTP requests to your app. Here you can choose which requests should be collected.
	|
	*/

	'requests' => [
		// With on-demand mode enabled, Clockwork will only profile requests when the browser extension is open or you
		// manually pass a "clockwork-profile" cookie or get/post data key.
		// Optionally you can specify a "secret" that has to be passed as the value to enable profiling.
		'on_demand' => false,

		// Collect only errors (requests with HTTP 4xx and 5xx responses)
		'errors_only' => false,

		// Response time threshold in miliseconds after which the request will be marked as slow
		'slow_threshold' => null,

		// Collect only slow requests
		'slow_only' => false,

		// Sample the collected requests (eg. set to 100 to collect only 1 in 100 requests)
		'sample' => false,

		// List of URIs that should not be collected
		'except' => [
			'/horizon/.*', // Laravel Horizon requests
			'/telescope/.*', // Laravel Telescope requests
			'/_debugbar/.*', // Laravel DebugBar requests
		],

		// List of URIs that should be collected, any other URI will not be collected if not empty
		'only' => [
			// '/api/.*'
		],

		// Don't collect OPTIONS requests, mostly used in the CSRF pre-flight requests and are rarely of interest
		'except_preflight' => true
	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Artisan commands collection
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork can collect data about executed artisan commands. Here you can enable and configure which commands
	| should be collected.
	|
	*/

	'artisan' => [
		// Enable or disable collection of executed Artisan commands
		'collect' => false,

		// List of commands that should not be collected (built-in commands are not collected by default)
		'except' => [
			// 'inspire'
		],

		// List of commands that should be collected, any other command will not be collected if not empty
		'only' => [
			// 'inspire'
		],

		// Enable or disable collection of command output
		'collect_output' => false,

		// Enable or disable collection of built-in Laravel commands
		'except_laravel_commands' => true
	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Queue jobs collection
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork can collect data about executed queue jobs. Here you can enable and configure which queue jobs should
	| be collected.
	|
	*/

	'queue' => [
		// Enable or disable collection of executed queue jobs
		'collect' => false,

		// List of queue jobs that should not be collected
		'except' => [
			// App\Jobs\ExpensiveJob::class
		],

		// List of queue jobs that should be collected, any other queue job will not be collected if not empty
		'only' => [
			// App\Jobs\BuggyJob::class
		]
	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Tests collection
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork can collect data about executed tests. Here you can enable and configure which tests should be
	| collected.
	|
	*/

	'tests' => [
		// Enable or disable collection of ran tests
		'collect' => false,

		// List of tests that should not be collected
		'except' => [
			// Tests\Unit\ExampleTest::class
		]
	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Enable data collection when Clockwork is disabled
	|------------------------------------------------------------------------------------------------------------------
	|
	| You can enable this setting to collect data even when Clockwork is disabled. Eg. for future analysis.
	|
	*/

	'collect_data_always' => false,

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Metadata storage
	|------------------------------------------------------------------------------------------------------------------
	|
	| Configure how is the metadata collected by Clockwork stored. Two options are available:
	|   - files - A simple fast storage implementation storing data in one-per-request files.
	|   - sql - Stores requests in a sql database. Supports MySQL, Postgresql, Sqlite and requires PDO.
	|
	*/

	'storage' => 'files',

	// Path where the Clockwork metadata is stored
	'storage_files_path' => storage_path('clockwork'),

	// Compress the metadata files using gzip, trading a little bit of performance for lower disk usage
	'storage_files_compress' => false,

	// SQL database to use, can be a name of database configured in database.php or a path to a sqlite file
	'storage_sql_database' => storage_path('clockwork.sqlite'),

	// SQL table name to use, the table is automatically created and udpated when needed
	'storage_sql_table' => 'clockwork',

	// Maximum lifetime of collected metadata in minutes, older requests will automatically be deleted, false to disable
	'storage_expiration' => 60 * 24 * 7,

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Authentication
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork can be configured to require authentication before allowing access to the collected data. This might be
	| useful when the application is publicly accessible. Setting to true will enable a simple authentication with a
	| pre-configured password. You can also pass a class name of a custom implementation.
	|
	*/

	'authentication' => false,

	// Password for the simple authentication
	'authentication_password' => 'VerySecretPassword',

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Stack traces collection
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork can collect stack traces for log messages and certain data like database queries. Here you can set
	| whether to collect stack traces, limit the number of collected frames and set further configuration. Collecting
	| long stack traces considerably increases metadata size.
	|
	*/

	'stack_traces' => [
		// Enable or disable collecting of stack traces
		'enabled' => true,

		// Limit the number of frames to be collected
		'limit' => 10,

		// List of vendor names to skip when determining caller, common vendors are automatically added
		'skip_vendors' => [
			// 'phpunit'
		],

		// List of namespaces to skip when determining caller
		'skip_namespaces' => [
			// 'Laravel'
		],

		// List of class names to skip when determining caller
		'skip_classes' => [
			// App\CustomLog::class
		]

	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Serialization
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork serializes the collected data to json for storage and transfer. Here you can configure certain aspects
	| of serialization. Serialization has a large effect on the cpu time and memory usage.
	|
	*/

	// Maximum depth of serialized multi-level arrays and objects
	'serialization_depth' => 10,

	// A list of classes that will never be serialized (eg. a common service container class)
	'serialization_blackbox' => [
		\Illuminate\Container\Container::class,
		\Illuminate\Foundation\Application::class,
	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Register helpers
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork comes with a "clock" global helper function. You can use this helper to quickly log something and to
	| access the Clockwork instance.
	|
	*/

	'register_helpers' => true,

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Send Headers for AJAX request
	|------------------------------------------------------------------------------------------------------------------
	|
	| When trying to collect data the AJAX method can sometimes fail if it is missing required headers. For example, an
	| API might require a version number using Accept headers to route the HTTP request to the correct codebase.
	|
	*/

	'headers' => [
		// 'Accept' => 'application/vnd.com.whatever.v1+json',
	],

	/*
	|------------------------------------------------------------------------------------------------------------------
	| Server-Timing
	|------------------------------------------------------------------------------------------------------------------
	|
	| Clockwork supports the W3C Server Timing specification, which allows for collecting a simple performance metrics
	| in a cross-browser way. Eg. in Chrome, your app, database and timeline event timings will be shown in the Dev
	| Tools network tab. This setting specifies the max number of timeline events that will be sent. Setting to false
	| will disable the feature.
	|
	*/

	'server_timing' => 10

];
