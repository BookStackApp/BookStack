<div id="tag-manager" entity-id="{{ isset($entity) ? $entity->id : 0 }}" entity-type="{{ $entity ? $entity->getType() : $entityType }}">
    <div class="tags">
        <p class="text-muted small">{!! nl2br(e(trans('entities.tags_explain'))) !!}</p>


        <draggable :options="{handle: '.handle'}" :list="tags" element="div">
            <div v-for="(tag, i) in tags" :key="tag.key" class="card drag-card">
                <div class="handle" >@icon('grip')</div>
                <div>
                    <autosuggest url="{{ url('/ajax/tags/suggest/names') }}" type="name" class="outline" :name="getTagFieldName(i, 'name')"
                                 v-model="tag.name" @input="tagChange(tag)" @blur="tagBlur(tag)" placeholder="{{ trans('entities.tag') }}"/>
                </div>
                <div>
                    <autosuggest url="{{ url('/ajax/tags/suggest/values') }}" type="value" class="outline" :name="getTagFieldName(i, 'value')"
                                 v-model="tag.value" @change="tagChange(tag)" @blur="tagBlur(tag)" placeholder="{{ trans('entities.tag_value') }}"/>
                </div>
                <div v-show="tags.length !== 1" class="text-center drag-card-action text-neg" @click="removeTag(tag)">@icon('close')</div>
            </div>
        </draggable>

        <button @click="addEmptyTag" type="button" class="text-button">{{ trans('entities.tags_add') }}</button>
    </div>
</div>