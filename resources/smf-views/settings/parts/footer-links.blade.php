{{--
$value - Setting value
$name - Setting input name
--}}
<div components="add-remove-rows"
     option:add-remove-rows:row-selector=".card"
     option:add-remove-rows:remove-selector="button.text-neg">

    <div component="sortable-list"
         option:sortable-list:handle-selector=".handle">
        @foreach(array_merge($value, [['label' => '', 'url' => '']]) as $index => $link)
            <div class="card drag-card {{ $loop->last ? 'hidden' : '' }}" @if($loop->last) refs="add-remove-rows@model" @endif>
                <div class="handle">@icon('grip')</div>
                @foreach(['label', 'url'] as $prop)
                    <div class="outline">
                        <input value="{{ $link[$prop] ?? '' }}"
                               placeholder="{{ trans('settings.app_footer_links_' . $prop) }}"
                               aria-label="{{ trans('settings.app_footer_links_' . $prop) }}"
                               name="{{ $name }}[{{ $loop->parent->last ? 'randrowid' : $index }}][{{$prop}}]"
                               type="text"
                               autocomplete="off"/>
                    </div>
                @endforeach
                <button type="button"
                        aria-label="{{ trans('common.remove') }}"
                        class="text-center drag-card-action text-neg">
                    @icon('close')
                </button>
            </div>
        @endforeach
    </div>

    <button refs="add-remove-rows@add" type="button" class="text-button">{{ trans('settings.app_footer_links_add') }}</button>
</div>