@if (count($pages) > 0)
        <ul class="contents">
            @foreach($pages as $page)
                <li><a href="#page-{{$page->id}}">{{ $page->name }}</a></li>
            @endforeach
        </ul>
@endif