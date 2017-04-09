@extends('base')

@section('content')

    <input type="hidden" name="searchTerm" value="{{$searchTerm}}">

<div id="search-system">
    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ baseUrl("/search/all?term={$searchTerm}") }}" class="text-button"><i class="zmdi zmdi-search"></i>{{ $searchTerm }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable id="searchSystem">

        <h1>{{ trans('entities.search_results') }}</h1>

        <input type="text" v-model="termString">

        <div class="row">

            <div class="col-md-6">
                @include('partials/entity-list', ['entities' => $entities])
            </div>

            <div class="col-md-5 col-md-offset-1">
               <h3>Search Filters</h3>

                <p><strong>Content Type</strong></p>
                <div class="form-group">
                    <label><input type="checkbox" v-on:change="typeChange" v-model="search.type.page" value="page"> Page</label>
                    <label><input type="checkbox" v-on:change="typeChange" v-model="search.type.chapter" value="chapter"> Chapter</label>
                    <label><input type="checkbox" v-on:change="typeChange" v-model="search.type.book" value="book"> Book</label>
                </div>


                <button type="button" class="button pos" v-on:click="updateSearch">Update Search</button>

            </div>

        </div>


    </div>
</div>

@stop