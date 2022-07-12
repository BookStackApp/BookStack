@extends('layouts.simple')

@section('body')
@include('common/nci_custom_styles')

<div style="margin:5px">
          <!-- search for nci -->
          @include('common/nci_search')
          <!-- end of search -->
          <div class="row mission" style="margin-top:40px;">
          @if(Session::has('message'))
          <div class="alert alert-success"> 
            {{Session::get('message')}}      
           </div> 
        @endif
          <h2 style="text-align:center">Customer Ratings Satisfaction Form</h2>
            <div class="definition" style="margin:30px;position: relative;
  left: 25%;
  right: 25%;">
            <h5 class="" >
            <ol type = "1">
            <div class="row">
            <div class="col-md-6">
                  <form method = "POST" action = "{{url('/add/user/ratings')}}">
                        @csrf
            <li>How did you find the experience of booking appointments?</li>
            <div class="form-control" name style="background-color: #FBF4F4;margin:5px;">
            <label class="inline">Very easy</label>
            <input type="radio" name="difficult" value="1">
            <input type="radio" name="difficult" value="2">
            <input type="radio" name="difficult" value="3">
            <input type="radio" name="difficult" value="4">
            <input type="radio" name="difficult" value="5">
            <label class="inline" style="    margin-left:20px;">Very difficult</label>
                        </div>
            <li>Were our staff empathetic to your needs?</li>
            <div class="form-control" style="background-color: #FBF4F4;margin:5px;">
            <label class="inline">Not Very empathetic</label>
<input type="radio" name="empathetic" value="1">
<input type="radio" name="empathetic" value="2">
<input type="radio" name="empathetic" value="3">
<input type="radio" name="empathetic" value="4">
<input type="radio" name="empathetic" value="5">
<label class="inline" style="    margin-left:20px;">Very empathetic</label>
            </div>
            <li> How long did you have to wait until the doctor attends to you?</li>
            <div class="form-control" style="background-color: #FBF4F4;margin:5px;">
            <label class="inline">Too long easy</label>
<input type="radio" name="long" value="1">
<input type="radio" name="long" value="2">
<input type="radio" name="long" value="3">
<input type="radio" name="long" value="4">
<input type="radio" name="long" value="5">
<label class="inline" style="    margin-left:20px;">Not at all</label>
            </div>
            <li>Were you satisfied with the doctor you were allocated with?</li>
            <div class="form-control" style="background-color: #FBF4F4;margin:5px;">
            <label class="inline">least satisfied</label>
<input type="radio" name="satisfied" value="1">
<input type="radio" name="satisfied" value="2">
<input type="radio" name="satisfied" value="3">
<input type="radio" name="satisfied" value="4">
<input type="radio" name="satisfied" value="5">
<label class="inline" style="    margin-left:20px;">Very satisfied</label>
            </div>
            <li>Any additional Comments?</li>
            <!-- <div class="form-control"> -->
            <input type="text" name="comment"  style="background-color: #FBF4F4;width: 100%;" class="form-control" id="comment" placeholder="Enter any additional comments here..." name="comment">
            
            <!-- </div> -->
            <div class="inline" style="float:right;margin-top:10px;">
  <button type="cancel" style="background-color: #DED9D9;border-radius:10px">Cancel</button>
  <button type="submit" style="background-color: #D820C5;border-radius:10px" value="Submit">Submit</button>
            </div>
      </form>
            </div>
            </div>
             </ol>
            </h5>
            </div>
            
          </div>
          <!--  end Requirements for Establishing a Basic Cancer Management Center-->
          <!-- footer start -->
      @include('common/nci_footer')
      <!-- footer end -->
</div>
@stop
