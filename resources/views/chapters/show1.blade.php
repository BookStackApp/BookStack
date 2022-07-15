@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')
<style>
    .col-lg-3 {
        width: 35%;
}
</style>
<div style="margin:5px">
<div class="mb-m print-hidden" style="margin-right:20;margin-left:20%;">
        @include('entities.breadcrumbs', ['crumbs' => [
            $chapter->book,
            $chapter,
        ]])
    </div>
          <!-- search for nci -->
          <!-- @include('common/nci_search') -->
          <!-- end of search -->
          <!-- <div class="row mission" style="margin-top:40px;">
          <h2 style="text-align:center">{{ $chapter->name }}</h2>
            <h3 class="card-text" style="text-align:center">
            {!! nl2br(e($chapter->description)) !!}
           </h3>
          </div> -->
        
          <!-- end of Services offered in a basic cancer center-->
          <!--  Requirements for Establishing a Basic Cancer Management Center-->
          <div class="row mission" style="margin-top:40px;">
          <!-- <div class="col-md-10"> -->
          <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>{{ $chapter->name }} Page Contents</h4></div>
          <div class="col-md-10">
          @foreach($pages as $page)
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <?php $type = $page->getType(); ?>
<a href="{{ $page->getUrl() }}"  data-page-type="{{$type}}" data-page-id="{{$page->id}}">
              <img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{$page->name}}
</h4></a>
            </div>
          </div>
        </div> @endforeach
          </div>
          <div class="col-md-2">
          <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links">
            @include('entities.meta', ['entity' => $chapter])

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

            @if($chapter->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $chapter))
                        <a href="{{ $chapter->getUrl('/permissions') }}" class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.chapters_permissions_active') }}</div>
                        </a>
                    @else
                        <div class="entity-meta-item">
                            @icon('lock')
                            <div>{{ trans('entities.chapters_permissions_active') }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            @if(userCan('page-create', $chapter))
                <a href="{{ $chapter->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(userCan('chapter-update', $chapter))
                <a href="{{ $chapter->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCanOnAny('chapter-create'))
                <a href="{{ $chapter->getUrl('/copy') }}" class="icon-list-item">
                    <span>@icon('copy')</span>
                    <span>{{ trans('common.copy') }}</span>
                </a>
            @endif
            @if(userCan('chapter-update', $chapter) && userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/move') }}" class="icon-list-item">
                    <span>@icon('folder')</span>
                    <span>{{ trans('common.move') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $chapter))
                <a href="{{ $chapter->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(signedInUser())
                @include('entities.favourite-action', ['entity' => $chapter])
            @endif
            @if(userCan('content-export'))
                @include('entities.export-menu', ['entity' => $chapter])
            @endif
        </div>
    </div>
          </div>
          <!-- </div>
          <div class="col-md-2">
          <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            @if(userCan('page-create', $chapter))
                <a href="{{ $chapter->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(userCan('chapter-update', $chapter))
                <a href="{{ $chapter->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCanOnAny('chapter-create'))
                <a href="{{ $chapter->getUrl('/copy') }}" class="icon-list-item">
                    <span>@icon('copy')</span>
                    <span>{{ trans('common.copy') }}</span>
                </a>
            @endif
            @if(userCan('chapter-update', $chapter) && userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/move') }}" class="icon-list-item">
                    <span>@icon('folder')</span>
                    <span>{{ trans('common.move') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $chapter))
                <a href="{{ $chapter->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(signedInUser())
                @include('entities.favourite-action', ['entity' => $chapter])
            @endif
            @if(userCan('content-export'))
                @include('entities.export-menu', ['entity' => $chapter])
            @endif
        </div>
    </div>
          </div> -->
        <!-- <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.equipment_requirements')}}
</h4></a>
            
            </div>
          </div>
        </div> -->
        <!-- <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.Human_Resource_Requirements')}}
</h4></a>
            </div>
          </div>
        </div> -->
        <!-- <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.Infrastructure_Requirements')}}
</h4></a>
            </div>
          </div>
        </div> -->

          </div>
          <!--  end Requirements for Establishing a Basic Cancer Management Center-->
          <!-- footer start -->
      @include('common/nci_footer')
      <!-- footer end -->
</div>
@stop
