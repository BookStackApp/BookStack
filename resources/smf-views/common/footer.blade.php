<footer>
    <span>© {{ now()->year }} ShreeMeera Foundation. Content originally published by Shree Vamanraj Prakashan, Pune.&nbsp;|&nbsp;<a href="{{env('MAIL_SUPPORT_ADDRESS')}}">Contact support</a></span>
</footer>
@if(count(setting('app-footer-links', [])) > 0)
<footer>
    @foreach(setting('app-footer-links', []) as $link)
        <a href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ strpos($link['label'], 'trans::') === 0 ? trans(str_replace('trans::', '', $link['label'])) : $link['label'] }}</a>
    @endforeach
</footer>
@endif