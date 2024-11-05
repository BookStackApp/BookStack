{{--
$type - string
$model - object
--}}
<div class="import-item text-{{ $type }} mb-xs">
    <p class="mb-none">@icon($type){{ $model->name }}</p>
    <div class="ml-s">
        <div class="text-muted">
            @if($model->attachments ?? [])
                <span>@icon('attach'){{ count($model->attachments) }}</span>
            @endif
            @if($model->images ?? [])
                <span>@icon('image'){{ count($model->images) }}</span>
            @endif
            @if($model->tags ?? [])
                <span>@icon('tag'){{ count($model->tags) }}</span>
            @endif
        </div>
        @foreach($model->chapters ?? [] as $chapter)
            @include('exports.parts.import-item', ['type' => 'chapter', 'model' => $chapter])
        @endforeach
        @foreach($model->pages ?? [] as $page)
            @include('exports.parts.import-item', ['type' => 'page', 'model' => $page])
        @endforeach
    </div>
</div>