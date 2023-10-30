@if(count(setting('app-footer-links', [])) > 0)
<footer class="print-hidden">
    @foreach(setting('app-footer-links', []) as $link)
        <a href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ strpos($link['label'], 'trans::') === 0 ? trans(str_replace('trans::', '', $link['label'])) : $link['label'] }}</a>
    @endforeach
</footer>
@endif