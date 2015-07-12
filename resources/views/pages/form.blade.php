{{ csrf_field() }}
<div class="page-title row">
    <div class="col-md-10">
        @include('form/text', ['name' => 'name', 'placeholder' => 'Enter Page Title'])
    </div>
    <div class="col-md-2">
        <button type="submit" class="button pos">Save</button>
    </div>
</div>
<div class="edit-area">
    @include('form/textarea', ['name' => 'html'])
</div>

