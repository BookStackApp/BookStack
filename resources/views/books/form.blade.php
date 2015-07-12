<div class="form-group">
    <label for="name">Name</label>
    @include('form/text', ['name' => 'name'])
</div>
<div class="form-group">
    <label for="description">Description</label>
    @include('form/textarea', ['name' => 'description'])
</div>
<button type="submit" class="button pos">Save</button>