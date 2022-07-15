@push('head')
    <script src="{{ url('/libs/tinymce/tinymce.min.js?ver=5.10.2') }}" nonce="{{ $cspNonce }}"></script>
@endpush

<!-- <div component="wysiwyg-editor"
     option:wysiwyg-editor:language="{{ config('app.lang') }}"
     option:wysiwyg-editor:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor:text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     option:wysiwyg-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:wysiwyg-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="flex-fill flex">

    <textarea id="html-editor"  name="html" rows="5"
          @if($errors->has('html')) class="text-neg" @endif>@if(isset($model) || old('html')){{ old('html') ? old('html') : $model->html }}@endif</textarea>
</div> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.3/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.3/adapters/jquery.js"></script>
<style type="text/css">
    .flex {
    min-height: 0;
    flex: 1;
    margin: 10px;
    max-width: 100%;
    overflow: auto;
}
</style>
<input type="hidden" name="textfr" value="{{ $model->id ?? 0 }}"/>
<input type="hidden" name="language" value="{{ config('app.lang') }}"/>
<input type="hidden" name="text_direction" value="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"/>
<input type="hidden" name="server_upload" value="{{ trans('errors.server_upload_limit') }}"/>
<input type="hidden" name="mage_upload_error_text" value="{{ trans('errors.image_upload_error') }}"/>
<div class="container">
    
    @if(count($pagedata) > 0)
    <div class="col-md-2" style="float: right;margin-bottom:-30px">
        <a href="javascript:void(0)" class="btn  addMore" style="background-color: #D820C5">
          <span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span> Add Content
        </a>
      </div>
    @foreach($pagedata as $page)
    <input type="text" name="pagesid" id="sectionTitle" class="form-control" value="{{$page->id}}">
    <input type="hidden" name="edited" id="sectionTitle" class="form-control" value="edited">

    <div class="row fieldGroup">
      <div class="col-md-10  ">
        <div class="form-group">
          <label for="sectionTitle">Section Title</label>
          <input type="text" name="sectionTitle1[]" id="sectionTitle" class="form-control" value="{{$page->page_sub_title}}">
        </div>
      </div>
      
      <div class="col-md-12  ">
        <div class="form-group">
          <h4>Section Content</h4>
          <textarea name="sectionContent1[]" class="editor" rows="5" placeholder="descriptions">{{$page->page_description}}</textarea>
        </div>
      </div>
    </div>
    @endforeach
    <div class="row" id="fieldGroupTemplate"style="display: none">
  <div class="col-md-10  ">
    <div class="form-group floating-label">
      <label for="sectionTitle">Section Title</label>
      <input type="text" name="newcontenttitle[]" id="sectionTitle" class="form-control">
    </div>
  </div>
  <div class="col-md-2  ">
    <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span> Remove</a>
  </div>
  <div class="col-sm-12 ">
    <div class="form-group">
      <h4>Section Content</h4>
      <textarea name="newcontent[]"></textarea>
    </div>
  </div>
</div>
    @endif
    @if(count($pagedata)==0)
    <div class="row fieldGroup">
      <div class="col-md-10  ">
        <div class="form-group">
          <label for="sectionTitle">Section Title</label>
          <input type="text" name="sectionTitle[]" id="sectionTitle" class="form-control">
        </div>
      </div>
      <div class="col-md-2  ">
        <a href="javascript:void(0)" class="btn addMore" style="background-color: #D820C5">
          <span class="glyphicon glyphicon glyphicon-plus" aria-hidden="true"></span> Add Content
        </a>
      </div>
      <div class="col-md-12  ">
        <div class="form-group">
          <h4>Section Content</h4>
          <textarea name="sectionContent[]" class="editor" rows="5" placeholder="descriptions"></textarea>
        </div>
      </div>
    </div>
  <div class="row" id="fieldGroupTemplate"style="display: none">
  <div class="col-md-10  ">
    <div class="form-group floating-label">
      <label for="sectionTitle">Section Title</label>
      <input type="text" name="sectionTitle[]" id="sectionTitle" class="form-control">
    </div>
  </div>
  <div class="col-md-2  ">
    <a href="javascript:void(0)" class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span> Remove</a>
  </div>
  <div class="col-sm-12 ">
    <div class="form-group">
      <h4>Section Content</h4>
      <textarea name="sectionContent[]"></textarea>
    </div>
  </div>
</div>
@endif
</div>



@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

@include('pages.parts.editor-translations')
<script type="text/javascript">
    $(function() {

//section add limit
var maxGroup = 10;

// initialize all current editor(s)
$('.editor').ckeditor();

//add more section
$(".addMore").click(function() {

  // define the number of existing sections
  var numGroups = $('.fieldGroup').length;

  // check whether the count is less than the maximum
  if (numGroups < maxGroup) {

    // create new section from template
    var $fieldHTML = $('<div>', {
      'class': 'row fieldGroup',
      'html': $("#fieldGroupTemplate").html()
    });

    // insert new group after last one
    $('.fieldGroup:last').after($fieldHTML);

    // initialize ckeditor on new textarea
    $fieldHTML.find('textarea').ckeditor();

  } else {
    alert('Maximum ' + maxGroup + ' sections are allowed.');
  }

});

//remove fields 
$("body").on("click", ".remove", function() {
  $(this).parents(".fieldGroup").remove();
});

});
</script>
@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

@include('pages.parts.editor-translations')