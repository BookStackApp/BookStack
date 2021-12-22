{{--
$selected - String name of the selected tab
$version - Version of bookstack to display
--}}
<div class="flex-container-row v-center wrap">
    <div class="py-m flex fit-content">
        @include('settings.parts.navbar', ['selected' => $selected])
    </div>
</div>
<div class="px-s">
    <hr class="darker m-none">
</div>
<div class="py-l px-m flex fit-content">
    <a target="_blank" rel="noopener noreferrer" href="https://github.com/BookStackApp/BookStack/releases">
        BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
    </a>
</div>