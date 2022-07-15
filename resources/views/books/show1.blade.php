@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')
      <!-- <div class="container px-xl py-s">  -->
      <div class="mb-s" style="margin-right:20;margin-left:20%;">
        @include('entities.breadcrumbs', ['crumbs' => [
            $book,
        ]])
    </div>
        <div style="margin:5px">
       <!-- start of types of cancer management centers  -->
      <div class="row mission">
      <div class="col-md-10">
      <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Cancer Management Centers level</h4></div>
     
     @foreach ($bookd as $books)
           <div class="col-md-4">
           <div class="card" >
       <div class="card-body">
     <!-- <a href="{{ url('/nci/mlevel/cancer/ceneter') }}"> -->
     <a href="{{ $books->getUrl() }}" class="" data-entity-type="book" data-entity-id="{{$books->id}}">
       <img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
         <h4 class="card-title management">{{ $books->name }}
     </h4>
     </a>
       </div>
     </div>
           </div>
           @endforeach
      </div>
      <!-- <div class="col-md-4">
      <div class="card" >
  <div class="card-body"> -->
<!-- <a href="{{ url('/nci/comprehensive/cancer/ceneter/') }}"> -->
  <!-- <img class="images" src="{{ asset('/uploads/mlcc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive Cancer Center
</h4> -->
<!-- </a> -->
  <!-- </div>
</div>
      </div> -->
      <div class="col-md-2" style="background-color: #FBF4F4;">
    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links">
            @include('entities.meta', ['entity' => $book])
            @if($book->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}" class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.books_permissions_active') }}</div>
                        </a>
                    @else
                        <div class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.books_permissions_active') }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

                <a href="{{ $book->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
                <a href="{{ $book->getUrl('/create-chapter') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.chapters_new') }}</span>
                </a>

            <hr class="primary-background">

            @if(userCan('book-update', $book))
                <a href="{{ $book->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
                <a href="{{ $book->getUrl('/sort') }}" class="icon-list-item">
                    <span>@icon('sort')</span>
                    <span>{{ trans('common.sort') }}</span>
                </a>
            @endif
            @if(userCan('book-create-all'))
                <a href="{{ $book->getUrl('/copy') }}" class="icon-list-item">
                    <span>@icon('copy')</span>
                    <span>{{ trans('common.copy') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $book))
                <a href="{{ $book->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('book-delete', $book))
                <a href="{{ $book->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background">

            @if(signedInUser())
                @include('entities.favourite-action', ['entity' => $book])
            @endif
            @if(userCan('content-export'))
                @include('entities.export-menu', ['entity' => $book])
            @endif
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
