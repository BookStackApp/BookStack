
{{ csrf_field() }}
<div class="form-group title-input">
    <label for="name">Chapter Name</label>
    @include('form/text', ['name' => 'name'])
</div>
<div class="form-group description-input">
    <label for="description">Description</label>
    @include('form/textarea', ['name' => 'description'])
</div>
<button type="submit" class="button pos">Save</button>