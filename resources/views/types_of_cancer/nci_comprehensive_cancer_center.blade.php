@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')

<div style="margin:5px">
          <!-- search for nci -->
          @include('common/nci_search')
          <!-- end of search -->
          <div class="row mission" style="margin-top:40px;">
          <h2 style="text-align:center">Comprehensive Cancer Center</h2>
            <div class="definition" style="margin:30px">
            <h5 class="" >
            This is any institution or facility able to provide highly specialized cancer services in addition to those provided 
            by the basic cancer treatment centre, including but not limited to:
            <ol type = "1">
            <li>Comprehensive pathology service</li>
            <li>Comprehensive radiology service</li>
            <li>Comprehensive medical laboratory service</li>
            <li>Specialized surgical oncology including reconstructive surger</li>
            <li>Comprehensive Radiation oncology</li>
            <li>Nuclear medicine</li>
            <li>Bone marrow transplant</li>
            <li>Oncology training program</li>
            <li>Cancer research agenda</li>
      </ol>
            It is expected that this centre will be a facility at level 6 as per the Kenya Essential

            Package of Health (KEPH).
            </h5>
            </div>
            
          </div>
          <!-- start definition -->
          <div class="row mission" style="margin-top:40px;">
          <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Services offered in a comprehensive cancer center</h4></div>
          <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive pathology
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive laboratory
</h4></a>
            
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive 
Radiotherapy
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Comprehensive Sugical 
Oncology
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Oncology Program
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Cancer Research
</h4></a>
            
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Screening and information 
Radiotherapy
</h4></a>
            </div>
          </div>
        </div>
          </div>
          <!-- end of Services offered in a basic cancer center-->
          <!--  Requirements for Establishing a Basic Cancer Management Center-->
          <div class="row mission" style="margin-top:40px;">
          <div style="background-color:white;text-align:center;margin-top:-30px;"><h4>Requirements for Establishing a Basic Cancer Management Center</h4></div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Operational Consideraton 
Requirements
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Equipment Requirements
</h4></a>
            
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Human Resource

Requirements
</h4></a>
            </div>
          </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
            <a href="#"><img class="images" src="{{ asset('/uploads/ccc.png') }}" alt="New york">
    <h4 class="card-title management">Physical
 Infrastructure Requirements
</h4></a>
            </div>
          </div>
        </div>

          </div>
          <!--  end Requirements for Establishing a Basic Cancer Management Center-->
          <!-- footer start -->
      @include('common/nci_footer')
      <!-- footer end -->
</div>
@stop
