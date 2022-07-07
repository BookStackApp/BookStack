@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')
      <!-- <div class="container px-xl py-s">  -->
        <div style="margin:5px">
      <div style="border: 3rem solid white;background-color:white;margin:5px;">
      <div style="background-color:white;text-align:center;margin-top:-40px;"><h4>NATIONAL GUIDELINES FOR ESTABLISHMENT OF CANCER MANAGEMENT CENTERS IN KENYA</h2></div>
      <div id="myCarousel" class="carousel slide" data-ride="carousel" style="background-color:white;">
     <!-- Indicators  -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

     <!-- Wrapper for slides  -->
    <div class="carousel-inner">
      <div class="item active">
        <img src="{{ asset('/uploads/img1.png') }}" alt="Los Angeles" style="width: 100%;
    height: 300px;
    object-fit: cover;">
      </div>

      <div class="item">
        <img src="{{ asset('/uploads/img1.png') }}" alt="Chicago" style="width: 100%;
    height: 300px;
    object-fit: cover;">
      </div>
      <div class="item">
        <p style="width: 100%;
    height: 300px;
    object-fit: cover;">The National Cancer Control Strategy 2017-2022 has provided strategic direction by prioritizing
decentralization of cancer services as per the Kenya Essential Package for Health to improve access to
cancer care and services. This guideline is the first comprehensive document to outline what is required to
establish a cancer management centre in Kenya. It provides a detailed account of what critical components
would need to be considered to establish a cancer centre. These include operational considerations of the
various units as well as physical infrastructural considerations and generic layouts, equipment and human
resource requirements across the entire cancer continuum. This guideline was developed in response to the
need for a model reference guideline of minimum priority medical equipment, infrastructure and human
resources required for cancer management, with the goal of increasing access to these services in Kenya.
The purpose of this guideline is to provide guidance to stakeholders on the setting up as well as the

successful operationalization of cancer centres.The National Cancer Control Strategy 2017-2022 has provided strategic direction by prioritizing
decentralization of cancer services as per the Kenya Essential Package for Health to improve access to
cancer care and services. This guideline is the first comprehensive document to outline what is required to
establish a cancer management centre in Kenya. It provides a detailed account of what critical components
would need to be considered to establish a cancer centre. These include operational considerations of the
various units as well as physical infrastructural considerations and generic layouts, equipment and human
resource requirements across the entire cancer continuum. This guideline was developed in response to the
need for a model reference guideline of minimum priority medical equipment, infrastructure and human
resources required for cancer management, with the goal of increasing access to these services in Kenya.
The purpose of this guideline is to provide guidance to stakeholders on the setting up as well as the

successful operationalization of cancer centres.</p>
      </div>
    
      <div class="item">
        <img src="{{ asset('/uploads/img1.png') }}" alt="New york" style="width: 100%;
    height: 300px;
    object-fit: cover;">
      </div>
    </div>

     <!-- Left and right controls  -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
           <!-- search for nci  -->
           @include('common/nci_search')
           
<div style="text-align:center;margin-top:25px;margin-bottom:-30px;">
              <!-- <div class="search-container">
    <form action="/action_page.php">
      <input type="text" style="background-color: #FBF4F4;width: 50%;border-radius: 5px;border-width:0px;
border:none;" placeholder="search here..." name="search">
      <button style="background-color: #D820C5;
    height: 40px;
    border-radius: 5px;
    margin-left: -5px;
    width: 75px;color:black" type="submit">Submit</button>
    </form>
  </div> -->
              </div>
           <!-- end of search  -->
      </div>
       <!-- mission vission core values  -->
      <div class="row mission">
      <div class="col-md-4">
      <div class="card mcard" >
  <div class="card-body">
    <h4 class="card-title">Our Mission
</h4>
    <h6>
    To oversee the delivery of
    responsive, high quality,
    sustainable and evidence based
    cancer prevention and control
    through multi sectoral

    coordinatin, regulation, advocacy
    and advancement of research
    </h6>
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <h4 class="card-title">Our Vision
</h4>
    <h6>
    

To be the leading authority in
cancer prevention and control

in Kenya
    </h6>
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <h4 class="card-title">Our Core Values
</h4>
    <h6>
<ul>
        <li>Integrity</li>
        <li>Innovation </li>
        <li>Competence</li>
        <li> Auality </li>
        <li>Equity</li>
        <li>Responsivenes</li>
        <li>Rights based approach</li>
      </ul>
    </h6>
  </div>
</div>
      </div>
      </div>
       <!-- start of types of cancer management centers  -->
      <div class="row mission">
      <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Types of Cancer Management Centers</h4></div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
   <!-- <a class="active" href="{{ url('/NATIONAL/CANCER/INSTITUTE/OF/KENYA') }}">{{ trans('entities.national_cancer_institute_of_kenya') }}</a>  -->
<!-- <a href="{{ url('/nci/basic/cancer/ceneter/') }}"> -->
  <img class="images" src="{{ asset('/uploads/bcc.png') }}" alt="New york">
    <h4 class="card-title management">Basic Cancer Center
</h4>
<!-- </a> -->
  </div>
</div>
      </div>

      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  
<!-- <a href="{{ url('/nci/mlevel/cancer/ceneter') }}"> -->
  <img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Middle Level Cancer Center
</h4>
<!-- </a> -->
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
<!-- <a href="{{ url('/nci/comprehensive/cancer/ceneter/') }}"> -->
  <img class="images" src="{{ asset('/uploads/mlcc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive Cancer Center
</h4>
<!-- </a> -->
  </div>
</div>
      </div>
      </div>
       <!-- end of cancer management centers start of downloadable contents  -->
      <div class="row mission">
      <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Downloadable Content</h4></div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <!-- <a href="#">--><img class="images" src="{{ asset('/uploads/download.png') }}" alt="New york">
    <h4 class="card-title management">Facility Inspection Forms
</h4>
<!-- </a> -->
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <!-- <a href="#">--><img class="images" src="{{ asset('/uploads/download.png') }}" alt="New york"> 
  <h4 class="card-title management">NCI Checklist Forms</h4>
<!-- </a> -->
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">-
  <!-- <a href="#"> -->
    <img class="images" src="{{ asset('/uploads/download.png') }}" alt="New york"> 
  <h4 class="card-title management">Patient Details Forms
</h4>
<!-- </a> -->
  </div>
</div>
      </div>
      </div>
      
       <!-- end of downloadble content 
      
       footer start -->
       @include('common/nci_footer')
      
       <!-- footer end  -->

</div>
     <!-- </div>  -->

    <!-- <div class="container" id="home-default">
        <div class="grid third gap-xxl no-row-gap" >
            <div>
                @if(count($draftPages) > 0)
                    <div id="recent-drafts" class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.my_recent_drafts') }}</h3>
                        <div class="px-m">
                            @include('entities.list', ['entities' => $draftPages, 'style' => 'compact'])
                        </div>
                    </div>
                @endif

                <div id="{{ auth()->check() ? 'recently-viewed' : 'recent-books' }}" class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')) }}</h3>
                    <div class="px-m">
                        @include('entities.list', [
                        'entities' => $recents,
                        'style' => 'compact',
                        'emptyText' => auth()->check() ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
                        ])
                    </div>
                </div>
            </div>

            <div>
                @if(count($favourites) > 0)
                    <div id="top-favourites" class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.my_most_viewed_favourites') }}</h3>
                        <div class="px-m">
                            @include('entities.list', [
                            'entities' => $favourites,
                            'style' => 'compact',
                            ])
                        </div>
                        <a href="{{ url('/favourites')  }}" class="card-footer-link">{{ trans('common.view_all') }}</a>
                    </div>
                @endif

                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.recently_updated_pages') }}</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                        'entities' => $recentlyUpdatedPages,
                        'style' => 'compact',
                        'emptyText' => trans('entities.no_pages_recently_updated'),
                        ])
                    </div>
                    <a href="{{ url("/pages/recently-updated") }}" class="card-footer-link">{{ trans('common.view_all') }}</a>
                </div>
            </div>

            <div>
                <div id="recent-activity">
                    <div class="card mb-xl">
                        <h3 class="card-title">{{ trans('entities.recent_activity') }}</h3>
                        @include('common.activity-list', ['activity' => $activity])
                    </div>
                </div>
            </div>

        </div>
    </div> -->

@stop
