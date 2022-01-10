<div class="image-manager-details">

    <form component="ajax-form"
          option:ajax-form:success-message="{{ trans('components.image_update_success') }}"
          option:ajax-form:method="put"
          option:ajax-form:response-container=".image-manager-details"
          option:ajax-form:url="{{ url('images/' . $image->id) }}">

        <div class="image-manager-viewer">
            <a href="{{ $image->url }}" target="_blank" rel="noopener" class="block">
                <img src="{{ $image->thumbs['display'] ?? $image->url }}"
                     alt="{{ $image->name }}"
                     class="anim fadeIn"
                     title="{{ $image->name }}">
            </a>
        </div>
        <div class="form-group stretch-inputs">
            <label for="name">{{ trans('components.image_image_name') }}</label>
            <input id="name" class="input-base" type="text" name="name" value="{{ $image->name }}">
        </div>
        <div class="grid half">
            <div>
                <button type="button"
                        id="image-manager-delete"
                        title="{{ trans('common.delete') }}"
                        class="button icon outline">@icon('delete')</button>
            </div>
            <div class="text-right">
                <button type="submit"
                        class="button icon outline">{{ trans('common.save') }}</button>
            </div>
        </div>
    </form>

    @if(!is_null($dependantPages))
        @if(count($dependantPages) > 0)
            <p class="text-neg mb-xs mt-m">{{ trans('components.image_delete_used') }}</p>
            <ul class="text-neg">
                @foreach($dependantPages as $page)
                    <li>
                        <a href="{{ $page->url }}"
                           target="_blank"
                           rel="noopener"
                           class="text-neg">{{ $page->name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
        <p class="text-neg mb-xs">{{ trans('components.image_delete_confirm_text') }}</p>
        <form component="ajax-form"
              option:ajax-form:success-message="{{ trans('components.image_delete_success') }}"
              option:ajax-form:method="delete"
              option:ajax-form:response-container=".image-manager-details"
              option:ajax-form:url="{{ url('images/' . $image->id) }}">
            <button type="submit" class="button neg">
                {{ trans('common.delete_confirm') }}
            </button>
        </form>
    @endif

</div>