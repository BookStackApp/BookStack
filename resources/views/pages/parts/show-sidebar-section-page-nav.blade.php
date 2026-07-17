@if(isset($pageNav) && count($pageNav))
    <nav id="page-navigation" class="mb-xl" aria-label="{{ trans('entities.pages_navigation') }}">
        <h5>{{ trans('entities.pages_navigation') }}</h5>
        <div class="body">
            <ul class="sidebar-page-nav menu">
                @foreach($pageNav as $navItem)
                    <li class="page-nav-item h{{ $navItem['level'] }}">
                        <a href="{{ $navItem['link'] }}" class="text-limit-lines-1 block">{{ $navItem['text'] }}</a>
                        <div class="link-background sidebar-page-nav-bullet"></div>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>
@endif