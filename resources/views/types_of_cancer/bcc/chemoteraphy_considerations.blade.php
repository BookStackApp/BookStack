@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')

<style>
	/* Custom style */
    .accordion-button::after {
        
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23333' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill-rule='evenodd' d='M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
      transform: scale(.8) !important;
      float: left !important;
    }
    .accordion-button:not(.collapsed)::after {
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23333' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill-rule='evenodd' d='M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
    }
    a, a:hover,a:visited, a:focus {
    text-decoration:none;
}
.card {
    background-color: #FBF4F4;
    border: #FBF4F4;
    display: none;
}
.accordion-button{
    background-color: #D9D9D9;
    margin-top:5px;
}
.accordion-item{
    border:0;
}
.accordion-button {
    float: left;
}
</style>
<div style="margin:5px">
          <!-- search for nci -->
          <div class="mb-m print-hidden" style="margin-right:20;margin-left:20%;">
        @include('entities.breadcrumbs', ['crumbs' => [
            $page->book,
            $page->hasChapter() ? $page->chapter : null,
            $page,
        ]])
    </div>
          <!-- end of search -->
          <!-- <div class="m-4"> -->
   
          <!-- start definition -->
          <!-- <div class="row mission" style="margin-top:40px;">
          <div style="background-color:white;text-align:center;margin-top:30px;"><h4>Chemotherapy: Operational Considerations</h4></div>
          <div class="col-xl-3 col-lg-2-0 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="{{url('/nci/books/chemoteraphy/considerations')}}"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.chemotherapy')}}
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-2-0 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.radiotherapy')}}
</h4></a>
            
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-2-0 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.nuclear_radiology')}}
</h4></a>
            </div>
          </div>
        </div>
       
        <div class="col-xl-3 col-lg-2-0 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.sugical_oncology')}}
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-2-0 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">{{trans('auth.screening_information')}}
</h4></a>
            </div>
          </div>
        </div> 
          </div> -->
          <!-- end of Services offered in a basic cancer center-->
          <!--  -->
          <!-- <div class="row mission" style="margin-top:40px;margin-bottom:30px;">
          <div style="background-color:white;text-align:center;margin-top:30px;"><h4>{{trans('auth.requiremnt')}}</h4></div>
         <div class="accordion" style="margin-bottom: 9px;" id="myAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne">1. What is HTML?</button>									
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>HTML stands for HyperText Markup Language. HTML is the standard markup language for describing the structure of web pages. <a href="https://www.tutorialrepublic.com/html-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree">3. What is CSS?</button>                     
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>A basic cancer treatment centre is able to provide at least one cancer treatment modality either as a stand-alone
facility (chemotherapy, radiotherapy or surgical oncology unit) or within the precincts of a hospital. In addition, it
should also be able to offer prevention, screening, early detection, diagnosis, registration, treatment, palliative
care and survivorship services. It is expected that this centre will be a facility at level 4 or above as per the Kenya

Essential Package of Health (KEPH).CSS stands for Cascading Style Sheet. CSS allows you to specify various style properties for a given HTML element such as colors, backgrounds, fonts etc. <a href="https://www.tutorialrepublic.com/css-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div>
    </div>
</div> -->
          <!--  -->
          <!-- <div class="row mission" style="margin-top:40px;margin-bottom:30px;">
          <div style="background-color:white;text-align:center;margin-top:30px;"><h4>{{trans('auth.requiremnt')}}</h4></div>
         <div class="accordion" style="margin-bottom: 9px;" id="myAccordion"> -->
        <!-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne">1. What is HTML?</button>									
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>HTML stands for HyperText Markup Language. HTML is the standard markup language for describing the structure of web pages. <a href="https://www.tutorialrepublic.com/html-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div> -->
        <!-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree">3. What is CSS?</button>                     
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>A basic cancer treatment centre is able to provide at least one cancer treatment modality either as a stand-alone
facility (chemotherapy, radiotherapy or surgical oncology unit) or within the precincts of a hospital. In addition, it
should also be able to offer prevention, screening, early detection, diagnosis, registration, treatment, palliative
care and survivorship services. It is expected that this centre will be a facility at level 4 or above as per the Kenya

Essential Package of Health (KEPH).CSS stands for Cascading Style Sheet. CSS allows you to specify various style properties for a given HTML element such as colors, backgrounds, fonts etc. <a href="https://www.tutorialrepublic.com/css-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div> -->
    <!-- </div>
</div> -->
<!-- nnzz -->

<div class="row mission" style="margin-top:40px;margin-bottom:30px;">
<div class="col-md-10">
          <div style="background-color:white;text-align:center;margin-top:30px;"><h4>{{$page->name}}</h4></div>
         <div class="accordion" style="margin-bottom: 9px;" id="myAccordion">
         <?php $type=0; $increment=1; ?>
        @if (isset($pageContent) && count($pageContent))
         @foreach($pageContent as $navItem)
         <?php $type++;$increment=$increment+(1/10);?>
         <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button style="height:5px" type="button" class="accordion-button collapsed " data-bs-toggle="collapse" data-bs-target="#collapseOne{{$type}}">{{$increment.' '. $navItem['page_sub_title'] }}</button>									
            </h2>
            <div id="collapseOne{{$type}}" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>{!! $navItem->page_description !!}</a></p>
                </div>
            </div>
        </div> @endforeach
        @endif
        <!-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree">3. What is CSS?</button>                     
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>A basic cancer treatment centre is able to provide at least one cancer treatment modality either as a stand-alone
facility (chemotherapy, radiotherapy or surgical oncology unit) or within the precincts of a hospital. In addition, it
should also be able to offer prevention, screening, early detection, diagnosis, registration, treatment, palliative
care and survivorship services. It is expected that this centre will be a facility at level 4 or above as per the Kenya

Essential Package of Health (KEPH).CSS stands for Cascading Style Sheet. CSS allows you to specify various style properties for a given HTML element such as colors, backgrounds, fonts etc. <a href="https://www.tutorialrepublic.com/css-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div> -->
    </div>
</div>
<div class="col-md-2">
<div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>

        <div class="icon-list text-primary">

            {{--User Actions--}}
            @if(userCan('page-update', $page))
                <a href="{{ $page->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCanOnAny('page-create'))
                <a href="{{ $page->getUrl('/copy') }}" class="icon-list-item">
                    <span>@icon('copy')</span>
                    <span>{{ trans('common.copy') }}</span>
                </a>
            @endif
            @if(userCan('page-update', $page))
                @if(userCan('page-delete', $page))
	                <a href="{{ $page->getUrl('/move') }}" class="icon-list-item">
	                    <span>@icon('folder')</span>
	                    <span>{{ trans('common.move') }}</span>
	                </a>
                @endif
                <a href="{{ $page->getUrl('/revisions') }}" class="icon-list-item">
                    <span>@icon('history')</span>
                    <span>{{ trans('entities.revisions') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $page))
                <a href="{{ $page->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('page-delete', $page))
                <a href="{{ $page->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background"/>

            @if(signedInUser())
                @include('entities.favourite-action', ['entity' => $page])
            @endif
            @if(userCan('content-export'))
                @include('entities.export-menu', ['entity' => $page])
            @endif
        </div>

    </div>
</div>
</div>
          <!--  Requirements for Establishing a Basic Cancer Management Center-->
          <!-- <div class="row mission" style="margin-top:40px;margin-bottom:30px;">
          <div style="background-color:white;text-align:center;margin-top:30px;"><h4>{{$page->name}}</h4></div>
         <div class="accordion" style="margin-bottom: 9px;" id="myAccordion">
         <//?php $type=0; ?>
         @if (isset($pageNav) && count($pageNav))
         @foreach($pageNav as $navItem)
         <//?php $type++ ?>
        <div class="accordion-item{{$type}}">
            <h2 class="accordion-header" id="headingOne{{$type}}">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#headingOne{{$type}}">{{ $navItem['text'] }}</button>									
            </h2>
            <div id="headingOne{{$type}}" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                @if (isset($diff) && $diff)
                    {!! $diff !!}
                @else
                    {!! isset($page->renderedHTML) ? $page->renderedHTML : $page->html !!}
                @endif
                </div>
            </div>
        </div>
        @endforeach
        @endif -->
        <!-- <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button style="height:5px" type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree">3. What is CSS?</button>                     
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#myAccordion">
                <div class="card-body">
                    <p>A basic cancer treatment centre is able to provide at least one cancer treatment modality either as a stand-alone
facility (chemotherapy, radiotherapy or surgical oncology unit) or within the precincts of a hospital. In addition, it
should also be able to offer prevention, screening, early detection, diagnosis, registration, treatment, palliative
care and survivorship services. It is expected that this centre will be a facility at level 4 or above as per the Kenya

Essential Package of Health (KEPH).CSS stands for Cascading Style Sheet. CSS allows you to specify various style properties for a given HTML element such as colors, backgrounds, fonts etc. <a href="https://www.tutorialrepublic.com/css-tutorial/" target="_blank">Learn more.</a></p>
                </div>
            </div>
        </div> -->
    <!-- </div>
</div> -->

          <!-- </div> -->

          <!--  end Requirements for Establishing a Basic Cancer Management Center-->
          <!-- footer start -->
      @include('common/nci_footer')
      <!-- footer end -->
</div>
@stop
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>