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
				<div class="carousel-parent smf-flex smf-flex-row smf-gap-x-16">

						<div class="recents smf-basis-3/12 smf-space-y-6 ">
							@include('home.parts.sidebar')
						</div>

						{{-- Start: Carousel --}}
						<div class="smf-carousel-with-progress smf-basis-9/12">
							@foreach($books as $key => $book)
								<div class="smf-card-with-progress" style="flex: none; order: 0; flex-grow: 0;">
										<div class="smf-card-header smf-bg-contain smf-bg-no-repeat smf-bg-center" style="background-image: url('{{ $book->getBookCover() }}');">
										</div>
										<div class="smf-progress-indicator">
												<div class="smf-whole-progress-indicator">
														<div class="smf-progress-value"></div>
												</div>
										</div>
								</div>
							@endforeach
						</div>
						{{-- End: Carousel --}}
				</div>
		</div>
@stop
