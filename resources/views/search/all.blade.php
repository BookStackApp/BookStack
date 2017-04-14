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

                <form v-on:submit="updateSearch" v-cloak>
                    <p><strong>Content Type</strong></p>
                    <div class="form-group">
                        <label><input type="checkbox" v-on:change="typeChange" v-model="search.type.page" value="page"> Page</label>
                        <label><input type="checkbox" v-on:change="typeChange" v-model="search.type.chapter" value="chapter"> Chapter</label>
                        <label><input type="checkbox" v-on:change="typeChange" v-model="search.type.book" value="book"> Book</label>
                    </div>

                    <p><strong>Exact Matches</strong></p>
                    <table cellpadding="0" cellspacing="0" border="0" class="no-style">
                        <tr v-for="(term, i) in search.exactTerms">
                            <td style="padding: 0 12px 6px 0;">
                                <input class="exact-input" v-on:input="exactChange" type="text" v-model="search.exactTerms[i]"></td>
                            <td>
                                <button type="button" class="text-button" v-on:click="removeExact(i)">
                                    <i class="zmdi zmdi-close-circle-o"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="text-button" v-on:click="addExact">
                                    <i class="zmdi zmdi-plus-circle-o"></i>Add exact match term
                                </button>
                            </td>
                        </tr>
                    </table>

                    <p><strong>Tag Searches</strong></p>
                    <table cellpadding="0" cellspacing="0" border="0" class="no-style">
                        <tr v-for="(term, i) in search.tagTerms">
                            <td style="padding: 0 12px 6px 0;">
                                <input class="tag-input" v-on:input="tagChange" type="text" v-model="search.tagTerms[i]"></td>
                            <td>
                                <button type="button" class="text-button" v-on:click="removeTag(i)">
                                    <i class="zmdi zmdi-close-circle-o"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="text-button" v-on:click="addTag">
                                    <i class="zmdi zmdi-plus-circle-o"></i>Add tag search
                                </button>
                            </td>
                        </tr>
                    </table>

                    <p><strong>Options</strong></p>
                    <label>
                        <input type="checkbox" v-on:change="optionChange('viewed_by_me')"
                               v-model="search.option.viewed_by_me" value="page">
                        Viewed by me
                    </label>
                    <label>
                        <input type="checkbox" v-on:change="optionChange('not_viewed_by_me')"
                               v-model="search.option.not_viewed_by_me" value="page">
                        Not viewed by me
                    </label>

                    <p><strong>Date Options</strong></p>
                    <table cellpadding="0" cellspacing="0" border="0" class="no-style">
                        <tr>
                            <td>Updated After</td>
                            <td style="padding: 0 12px 6px 0;">
                                <input v-if="search.dates.updated_after" class="tag-input" v-on:input="tagChange" type="date" v-model="search.dates.updated_after" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
                                <button type="button" class="text-button" v-if="!search.dates.updated_after" v-on:click="enableDate('updated_at')">Set Date</button>
                            </td>
                            <td>
                                <button v-if="search.dates.updated_after" type="button" class="text-button" v-on:click="search.dates.updated_after = false">
                                    <i class="zmdi zmdi-close-circle-o"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="button" class="text-button" v-on:click="addTag">
                                    <i class="zmdi zmdi-plus-circle-o"></i>Add tag search
                                </button>
                            </td>
                        </tr>
                    </table>


                    <button type="submit" class="button pos">Update Search</button>
                </form>



            </div>

        </div>


    </div>
</div>

@stop