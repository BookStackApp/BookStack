@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')

<div style="margin:5px">
          <!-- search for nci -->
          @include('common/nci_search')
          <!-- end of search -->
          <div class="row mission" style="margin-top:40px;">
          <div class="col-md-6" style="margin-left:25%;margin-right:25%;  height: 30vh;">
                 
          <h5>Cancer Management Center Facility Forms Download</h5>
            <ol type="1">
            <li><a href="#"> Application form to NCI-3</a></li>
            <li><a href="#"> Generic layout design (chemotherapy unit).</a></li>
            <li><a href="#"> Generic layout design (Radiation unit).</a></li>
            <li><a href="#"> Generic layout design (Nuclear medicine unit).</a></li>
            <li><a href="#"> Checklist for a medical cyclotron Facility</a></li>
            <li><a href="#"> PPE list</a></li>
         </ol>
        </div>
          </div>
          <!-- start definition -->
          
          <!--  Requirements for Establishing a Basic Cancer Management Center-->
          <div class="row mission" style="margin-top:40px;">
          <div class="col-md-6" style="margin-left:25%;margin-right:25%; height: 30vh;">
          <h5>NCI-K Inspection Forms</h5>
          <ol type="1">
            <li><a href="#"> NCI Inspection Checklist</a></li>
            <li><a href="#"> Inspection checklist for a medical cyclotron facility.</a></li>
            <li><a href="#"> PPE list</a></li>
         </ol>
        </div>
          </div>
          <div class="row mission" style="margin-top:40px;">
          <div class="col-md-6" style="margin-left:25%;margin-right:25%; height: 30vh;">
          <h5>Cancer Center Patient Forms</h5>
          <ol type="1">
            <li><a href="#"> Cancer Abstract Form</a></li>
            <li><a href="#"> Cancer Screening and Early Diagnosis Form</a></li>
            <li><a href="#"> Cancer Treatment Informed Consent Form</a></li>
            <li><a href="#"> Chemotherapy Administration Form</a></li>
            <li><a href="#"> Medication Label</a></li>
            <li><a href="#"> New Patient Oncology Assessment Form</a></li>
            <li><a href="#"> Pain Assessment Tools</a></li>
         </ol>
        </div>
       
          </div>
          <!--  end Requirements for Establishing a Basic Cancer Management Center-->
          <!-- footer start -->
      @include('common/nci_footer')
      <!-- footer end -->
</div>
@stop
