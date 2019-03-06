<h1>{{ trans('entities.tags') }}</h1>
<div class="tag grid">
    @if(count($tags) > 0)
            @php
                $charactersplit = '';
            @endphp
            @foreach($tags as $tag)
                @if (substr(strtoupper($tag->name),0,1) != $charactersplit)
                    @if ('' != $charactersplit)
                        </div>
                    @endif
                    @php
                        $charactersplit = substr(strtoupper($tag->name),0,1);
                    @endphp
                    <div class="taggroup entity-list-item grid-content"  data-entity-type="taggroup" data-entity-id="{{$charactersplit}}">
                        <h4><a class="entity-list-item-link" href="/tags/{{$charactersplit}}">{{$charactersplit}}</a></h4>
                @endif
                @include('tags/list-item', ['tag' => $tag])
            @endforeach
    @else
        <p class="text-muted">Tag not found</p>
    @endif
</div>