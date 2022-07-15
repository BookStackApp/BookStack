@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')
      <!-- <div class="container px-xl py-s">  -->
        <div style="margin:5px">
       <!-- start of types of cancer management centers  -->
      <div class="row mission">
      <div class="col-md-10">
     @foreach ($books as $book)
           <div class="col-md-4">
           <div class="card" style="background-color:none" >
       <div class="card-body">
     <!-- <a href="{{ url('/nci/mlevel/cancer/ceneter') }}"> -->
     <a href="{{ $book->getUrl() }}" class="" data-entity-type="book" data-entity-id="{{$book->id}}">
     <div class="bg-{{ $book->getType() }} featured-image-container-wrap">
        <div class="featured-image-container" @if($book->cover) style="background-image: url('{{ $book->getBookCover() }}')"@endif>
        </div>
        @icon($book->getType())
        
    </div>
    <h4 class="card-title management">{{ $book->name }}
     </h4>
     </a>
       </div>
     </div>
           </div>
           @endforeach
      </div>
      <div class="col-md-2" style="background-color: #FBF 4F4;">
      <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            @if(user()->can('book-create-all'))
                <a href="{{ url("/create-book") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.books_create') }}</span>
                </a>
            @endif

            @include('entities.view-toggle', ['view' => $view, 'type' => 'books'])

            <a href="{{ url('/tags') }}" class="icon-list-item">
                <span>@icon('tag')</span>
                <span>{{ trans('entities.tags_view_tags') }}</span>
            </a>
        </div>
    </div>
      </div>
      </div>
       

</div>
<!-- footer start -->
@include('common/nci_footer')
      
      <!-- footer end  -->
     <!-- </div>  -->

    

@stop
