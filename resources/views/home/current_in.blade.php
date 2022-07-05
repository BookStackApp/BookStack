@extends('layouts.simple')

@section('body')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
.carousel-control .glyphicon-chevron-right, .carousel-control .icon-next {
    right: -15%;
    margin-right: -10px;
}
.carousel-control .glyphicon-chevron-left, .carousel-control .icon-prev {
    left: -15%;
    margin-left: -10px;
}
body {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
    color: #333;
    background-color:#FBF4F4;
}
#myCarousel{
background-color:white;

}
h2{
text-align:center;
}
.topnav .search-container {
  float: right;
}

.topnav input[type=text] {
  padding: 6px;
  margin-top: 8px;
  font-size: 17px;
  border: none;
}

.topnav .search-container button {
  float: right;
  padding: 6px;
  margin-top: 8px;
  margin-right: 16px;
  background-color: #FBF4F4;
  font-size: 17px;
  border: none;
  cursor: pointer;
}

.topnav .search-container button:hover {
    background-color: #FBF4F4;
}

@media screen and (max-width: 600px) {
  .topnav .search-container {
    float: none;
  }
  .topnav a, .topnav input[type=text], .topnav .search-container button {
    float: none;
    display: block;
    text-align: left;
    width: 100%;
    margin: 0;
    padding: 14px;
  }
  .topnav input[type=text] {
    background-color: #FBF4F4;  
  }
}
.card{
    width: 25rem;height:15rem;
    background-color: #FBF4F4; 
    margin-left:100px;
    width: 300px;
    height: 195px;
}
.mission{
    grid-gap: 10px;
    border: 4rem solid white;background-color:white;margin-top:30px;margin-left:-200px;margin-right:-200px;
}
h6{
    margin: 10px;
}
.management{
    text-align:center;
}
.images{
    width: 100%;
    height: 195px;
    object-fit: cover;
    
}
</style>
     <!-- <div class="container px-xl py-s"> -->
        <div class="container">
      <div style="border: 4rem solid white;background-color:white;margin-top:30px;margin-left:-200px;margin-right:-200px;">
      <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>NATIONAL GUIDELINES FOR ESTABLISHMENT OF CANCER MANAGEMENT CENTERS IN KENYA</h2></div>
      <div id="myCarousel" class="carousel slide" data-ride="carousel" style="background-color:white;">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
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

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
              <div style="background-color:white;text-align:center;margin-top:25px;margin-bottom:-30px;">
              <div class="search-container">
    <form action="/action_page.php">
      <input type="text" style="background-color: #FBF4F4;width: 50%;border-radius: 5px;border-width:0px;
border:none;" placeholder="search here..." name="search">
      <button style="background-color: #D820C5;
    height: 40px;
    border-radius: 5px;
    margin-left: -5px;
    width: 75px;color:black" type="submit">Submit</button>
    </form>
  </div>
              </div>

      </div>
      <!-- mission vission core values -->
      <div class="row mission">
      <div class="col-md-4">
      <div class="card" >
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
        <li>IntegritG</li>
        <li>InnovatioP </li>
        <li>CompetencM</li>
        <li> AualitG </li>
        <li>EquitG</li>
        <li>Responsivenes8</li>
        <li>Rights based approach</li>
      </ul>
    </h6>
  </div>
</div>
      </div>
      </div>
      <!-- start of types of cancer management centers -->
      <div class="row mission">
      <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Types of Cancer Management Centers</h4></div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
    
<img class="images" src="{{ asset('/uploads/bcc.png') }}" alt="New york">
    <h4 class="card-title management">Basic Cancer Center
</h4>
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  
<img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Middle Level Cancer Center
</h4>
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
<img class="images" src="{{ asset('/uploads/mlcc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive Cancer Center
</h4>
  </div>
</div>
      </div>
      </div>
      <!-- end of cancer management centers -->

      <!-- start of downloadable contents -->
      <div class="row mission">
      <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Downloadable Content</h4></div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <img class="images" src="{{ asset('/uploads/download.png') }}" alt="New york">
    <h4 class="card-title management">Facility Inspection Forms
</h4>
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <img class="images" src="{{ asset('/uploads/download.png') }}" alt="New york">
  <h4 class="card-title management">NCI Checklist Forms
</h4>
  </div>
</div>
      </div>
      <div class="col-md-4">
      <div class="card" >
  <div class="card-body">
  <img class="images" src="{{ asset('/uploads/download.png') }}" alt="New york">
  <h4 class="card-title management">Patient Details Forms
</h4>
  </div>
</div>
      </div>
      </div>
      <!-- end of downloadble content -->
      <!-- footer start -->
      <div class="row mission" style="height: 200px;margin-bottom:30px;background:#FBF4F4">
       <div class="col-md-6">
       <h4 class="card-title management" >
      <p>Customer Satisfaction Ratings</p><br>
<p>Click to access our customer</p><br>

<p>satisfaction rating form</p><br>
</h4>
       </div>
      <div class="col-md-6">
  <h4 class="card-title management">
    <p>Contact Information</p><br><br>

    <p>Tel: +254712345678, +254733112233</p>
     <p>Social media:</p><br>
</h4>
       </div>
      </div>
      <!-- footer end -->

</div>
    <!-- </div> -->

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
