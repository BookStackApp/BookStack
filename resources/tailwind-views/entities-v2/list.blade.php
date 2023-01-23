<div class="smf-sm:p-8 smf-dark:bg-gray-800 dark:border-gray-700 smf-w-full smf-max-w-md smf-rounded-lg smf-border smf-bg-white smf-p-4 smf-shadow-md">
	<div class="smf-mb-4 smf-flex smf-items-center smf-justify-between">
			<h5 class="smf-dark:text-white smf-text-xl smf-font-bold smf-leading-none smf-text-gray-900">
				{{ $sectionHeader ?? '' }}
			</h5>
			<a href="{{ $viewAllUrl ?? '/' }}" class="hover:underline dark:text-blue-500 smf-text-sm smf-font-medium smf-text-blue-600">
				{{ trans('common.view_all') }}
			</a>
	</div>
	@if(count($entities) > 0)
		<div class="smf-flow-root">
			<ul role="list" class="smf-divide-y smf-divide-gray-200">
				@foreach($entities as $index => $entity)
					@if($entity->book)
						<li class="smf-sm:py-4 smf-border-solid smf-py-3">
								<div class="smf-flex smf-items-center smf-space-x-4">
										<div class="smf-min-w-0 smf-flex-1">
												<p class="dark:text-white smf-text-ellipsis smf-text-sm smf-font-medium smf-text-gray-900">
													{{ $entity->preview_name ?? $entity->name }}
												</p>
												<p class="dark:text-white smf-text-ellipsis smf-text-sm smf-font-medium smf-text-gray-900">
														{{ $entity->book->getShortName(42) }}
												</p>
												@if($entity->chapter)
													<p class="dark:text-white smf-text-ellipsis smf-text-sm smf-font-medium smf-text-gray-900">
														<span class="text-muted entity-list-item-path-sep">@icon('chevron-right')</span> {{ $entity->chapter->getShortName(42) }}
													</p>
												@endif
												</p>
												<p class="dark:text-gray-400 smf-text-ellipsis smf-text-sm smf-text-gray-500">
													{{ $entity->preview_content ?? $entity->getExcerpt() }}
												</p> 
												@if($entity->tags->count() > 0)
														<div class="entity-item-tags mt-xs">
																@include('entities.tag-list', ['entity' => $entity, 'linked' => false ])
														</div>
												@endif
										</div>
										<div class="smf-dark:text-white smf-inline-flex smf-items-center smf-text-base smf-font-semibold smf-text-gray-900">
												<svg xmlns="http://www.w3.org/2000/svg" class="smf-h-4 smf-w-4" fill="none" stroke="var(--color-bookshelf)" stroke-width="1.5" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round"
																d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
												</svg>
										</div>
								</div>
						</li>
					@endif
				@endforeach	
			</ul>
		</div>
	@else
		<p class="text-muted empty-text">
			{{ $emptyText ?? trans('common.no_items') }}
		</p>
	@endif
</div>