@if (count($favourites) > 0)
		<div id="top-favourites" class="mb-xl">
				@include('entities-v2.list', [
						'entities' => $favourites,
						'sectionHeader' => trans('entities.my_most_viewed_favourites'),
						'style' => 'compact',
						'viewAllUrl' => url('/favourites'),
				])
		</div>
@endif

<div class="mb-xl">
		@include('entities-v2.list', [
				'entities' => $recents,
				'sectionHeader' => trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')),
				'style' => 'compact',
				'viewAllUrl' => url('/favourites'),
				'emptyText' => auth()->check() ? trans('entities.no_pages_viewed') : trans('entities.books_empty'),
		])
</div>
