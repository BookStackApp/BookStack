@extends('layouts.simple')

@section('body')

    <div class="container px-xl py-s">
        <div class="grid half">
            <div>
                <div class="icon-list inline block">
                    @include('home.parts.expand-toggle', [
                        'classes' => 'text-muted text-primary',
                        'target' => '.entity-list.compact .entity-item-snippet',
                        'key' => 'home-details',
                    ])
                </div>
            </div>
            <div class="text-m-right">
                <div class="icon-list inline block">
                    @include('common.dark-mode-toggle', [
                        'classes' => 'text-muted icon-list-item text-primary',
                    ])
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="home-default">

        <div class="smf-flex smf-flex-row carousel-parent smf-gap-x-16">

            <div class="recents smf-basis-3/12">

                <div
                    class="smf-w-full smf-max-w-md smf-p-4 smf-bg-white smf-border smf-rounded-lg smf-shadow-md smf-sm:p-8 smf-dark:bg-gray-800 dark:border-gray-700">
                    <div class="smf-flex smf-items-center smf-justify-between smf-mb-4">
                        <h5 class="smf-text-xl smf-font-bold smf-leading-none smf-text-gray-900 smf-dark:text-white">Saved
                            Searches</h5>
                        <a href="#"
                            class="smf-text-sm smf-font-medium smf-text-blue-600 hover:underline dark:text-blue-500">
                            View all
                        </a>
                    </div>
                    <div class="smf-flow-root">
                        <ul role="list" class="smf-divide-y smf-divide-gray-200">
                            <li class="smf-py-3 smf-sm:py-4 smf-border-solid">
                                <div class="smf-flex smf-items-center smf-space-x-4">

                                    <div class="smf-flex-1 smf-min-w-0">
                                        <p
                                            class="smf-text-sm smf-font-medium smf-text-gray-900 smf-truncate dark:text-white">
                                            Neil Sims
                                        </p>
                                        <p class="smf-text-sm smf-text-gray-500 smf-truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="smf-inline-flex smf-items-center smf-text-base smf-font-semibold smf-text-gray-900 smf-dark:text-white">
										<svg xmlns="http://www.w3.org/2000/svg" class="smf-w-4 smf-h-4" fill="none" stroke="var(--color-bookshelf)" stroke-width="1.5" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
										</svg>
                                    </div>
                                </div>
                            </li>
                            <li class="smf-py-3 smf-sm:py-4 smf-border-solid">
                                <div class="smf-flex smf-items-center smf-space-x-4">

                                    <div class="smf-flex-1 smf-min-w-0">
                                        <p
                                            class="smf-text-sm smf-font-medium smf-text-gray-900 smf-truncate dark:text-white">
                                            Bonnie Green
                                        </p>
                                        <p class="smf-text-sm smf-text-gray-500 smf-truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="smf-inline-flex smf-items-center smf-text-base smf-font-semibold smf-text-gray-900 dark:text-white">
                                        $3467
                                    </div>
                                </div>
                            </li>


                        </ul>
                    </div>
                </div>

            </div>

            {{-- Start:  Carousel --}}
            <div class="smf-carousel-with-progress smf-basis-9/12">
                <div class="smf-card-with-progress" style="flex: none; order: 0; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 1; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 2; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 3; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 4; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 3; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 4; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 3; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 4; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"></div>
                        </div>
                    </div>
                </div>

                <div class="smf-card-with-progress" style="flex: none; order: 5; flex-grow: 0;">
                    <div class="smf-card-header"
                        style="background-image: url('https://cdn.builder.io/api/v1/image/assets%2FTEMP%2Fb2b12f2366a84ae0a00ce02e94acc5c9');">
                    </div>
                    <div class="smf-progress-indicator">
                        <div class="smf-whole-progress-indicator">
                            <div class="smf-progress-value"</div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- End: Carousel --}}
            </div>
        </div>

    @stop
