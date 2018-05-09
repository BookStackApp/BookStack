<table>
    <tbody>
    @foreach($entity->tags as $tag)
        <tr class="tag">
            <td @if(!$tag->value) colspan="2" @endif><a href="{{ baseUrl('/search?term=%5B' . urlencode($tag->name) .'%5D') }}">{{ $tag->name }}</a></td>
            @if($tag->value) <td class="tag-value"><a href="{{ baseUrl('/search?term=%5B' . urlencode($tag->name) .'%3D' . urlencode($tag->value) . '%5D') }}">{{$tag->value}}</a></td> @endif
        </tr>
    @endforeach
    </tbody>
</table>