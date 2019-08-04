@extends('simple-layout')

@section('body')
    <input type="hidden" name="searchTerm" value="{{$searchTerm}}">

    <div class="container" id="search-system">

        <div class="my-s">
            &nbsp;
        </div>

        <div class="grid right-focus reverse-collapse gap-xl">
            <div>
                <div>
                    <h5>{{ trans('entities.search_filters') }}</h5>

                    <form v-on:submit="updateSearch" v-cloak class="v-cloak anim fadeIn">
                        <h6 class="text-muted">{{ trans('entities.search_content_type') }}</h6>
                        <div class="form-group">
                            <label class="inline checkbox text-page"><input type="checkbox" v-on:change="typeChange" v-model="search.type.page" value="page">{{ trans('entities.page') }}</label>
                            <label class="inline checkbox text-chapter"><input type="checkbox" v-on:change="typeChange" v-model="search.type.chapter" value="chapter">{{ trans('entities.chapter') }}</label>
                            <br>
                            <label class="inline checkbox text-book"><input type="checkbox" v-on:change="typeChange" v-model="search.type.book" value="book">{{ trans('entities.book') }}</label>
                            <label class="inline checkbox text-bookshelf"><input type="checkbox" v-on:change="typeChange" v-model="search.type.bookshelf" value="bookshelf">{{ trans('entities.shelf') }}</label>
                        </div>

                        <h6 class="text-muted">{{ trans('entities.search_exact_matches') }}</h6>
                        <table cellpadding="0" cellspacing="0" border="0" class="no-style">
                            <tr v-for="(term, i) in search.exactTerms">
                                <td style="padding: 0 12px 6px 0;">
                                    <input class="exact-input outline" v-on:input="exactChange" type="text" v-model="search.exactTerms[i]"></td>
                                <td>
                                    <button type="button" class="text-neg text-button" v-on:click="removeExact(i)">
                                        @icon('close')
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="button" class="text-button" v-on:click="addExact">
                                        @icon('add-circle'){{ trans('common.add') }}
                                    </button>
                                </td>
                            </tr>
                        </table>

                        <h6 class="text-muted">{{ trans('entities.search_tags') }}</h6>
                        <table cellpadding="0" cellspacing="0" border="0" class="no-style">
                            <tr v-for="(term, i) in search.tagTerms">
                                <td style="padding: 0 12px 6px 0;">
                                    <input class="tag-input outline" v-on:input="tagChange" type="text" v-model="search.tagTerms[i]"></td>
                                <td>
                                    <button type="button" class="text-neg text-button" v-on:click="removeTag(i)">
                                        @icon('close')
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="button" class="text-button" v-on:click="addTag">
                                        @icon('add-circle'){{ trans('common.add') }}
                                    </button>
                                </td>
                            </tr>
                        </table>

                        @if(signedInUser())
                            <h6 class="text-muted">{{ trans('entities.search_options') }}</h6>
                            <label class="checkbox">
                                <input type="checkbox" v-on:change="optionChange('viewed_by_me')"
                                       v-model="search.option.viewed_by_me" value="page">
                                {{ trans('entities.search_viewed_by_me') }}
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" v-on:change="optionChange('not_viewed_by_me')"
                                       v-model="search.option.not_viewed_by_me" value="page">
                                {{ trans('entities.search_not_viewed_by_me') }}
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" v-on:change="optionChange('is_restricted')"
                                       v-model="search.option.is_restricted" value="page">
                                {{ trans('entities.search_permissions_set') }}
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" v-on:change="optionChange('created_by:me')"
                                       v-model="search.option['created_by:me']" value="page">
                                {{ trans('entities.search_created_by_me') }}
                            </label>
                            <label class="checkbox">
                                <input type="checkbox" v-on:change="optionChange('updated_by:me')"
                                       v-model="search.option['updated_by:me']" value="page">
                                {{ trans('entities.search_updated_by_me') }}
                            </label>
                        @endif

                        <h6 class="text-muted">{{ trans('entities.search_date_options') }}</h6>
                        <table cellpadding="0" cellspacing="0" border="0" class="no-style form-table">
                            <tr>
                                <td width="200">{{ trans('entities.search_updated_after') }}</td>
                                <td width="80">
                                    <button type="button" class="text-button" v-if="!search.dates.updated_after"
                                            v-on:click="enableDate('updated_after')">{{ trans('entities.search_set_date') }}</button>

                                </td>
                            </tr>
                            <tr v-if="search.dates.updated_after">
                                <td>
                                    <input v-if="search.dates.updated_after" class="tag-input"
                                           v-on:input="dateChange('updated_after')" type="date" v-model="search.dates.updated_after"
                                           pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
                                </td>
                                <td>
                                    <button v-if="search.dates.updated_after" type="button" class="text-neg text-button"
                                            v-on:click="dateRemove('updated_after')">
                                        @icon('close')
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans('entities.search_updated_before') }}</td>
                                <td>
                                    <button type="button" class="text-button" v-if="!search.dates.updated_before"
                                            v-on:click="enableDate('updated_before')">{{ trans('entities.search_set_date') }}</button>

                                </td>
                            </tr>
                            <tr v-if="search.dates.updated_before">
                                <td>
                                    <input v-if="search.dates.updated_before" class="tag-input"
                                           v-on:input="dateChange('updated_before')" type="date" v-model="search.dates.updated_before"
                                           pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
                                </td>
                                <td>
                                    <button v-if="search.dates.updated_before" type="button" class="text-neg text-button"
                                            v-on:click="dateRemove('updated_before')">
                                        @icon('close')
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans('entities.search_created_after') }}</td>
                                <td>
                                    <button type="button" class="text-button" v-if="!search.dates.created_after"
                                            v-on:click="enableDate('created_after')">{{ trans('entities.search_set_date') }}</button>

                                </td>
                            </tr>
                            <tr v-if="search.dates.created_after">
                                <td>
                                    <input v-if="search.dates.created_after" class="tag-input"
                                           v-on:input="dateChange('created_after')" type="date" v-model="search.dates.created_after"
                                           pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
                                </td>
                                <td>
                                    <button v-if="search.dates.created_after" type="button" class="text-neg text-button"
                                            v-on:click="dateRemove('created_after')">
                                        @icon('close')
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans('entities.search_created_before') }}</td>
                                <td>
                                    <button type="button" class="text-button" v-if="!search.dates.created_before"
                                            v-on:click="enableDate('created_before')">{{ trans('entities.search_set_date') }}</button>

                                </td>
                            </tr>
                            <tr v-if="search.dates.created_before">
                                <td>
                                    <input v-if="search.dates.created_before" class="tag-input"
                                           v-on:input="dateChange('created_before')" type="date" v-model="search.dates.created_before"
                                           pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
                                </td>
                                <td>
                                    <button v-if="search.dates.created_before" type="button" class="text-neg text-button"
                                            v-on:click="dateRemove('created_before')">
                                        @icon('close')
                                    </button>
                                </td>
                            </tr>
                        </table>


                        <button type="submit" class="button primary">{{ trans('entities.search_update') }}</button>
                    </form>

                </div>
            </div>
            <div>
                <div v-pre class="card content-wrap">
                    <h1 class="list-heading">{{ trans('entities.search_results') }}</h1>
                    <form action="{{ url('/search') }}" method="GET"  class="search-box flexible hide-over-l">
                        <input value="{{$searchTerm}}" type="text" name="term" placeholder="{{ trans('common.search') }}">
                        <button type="submit">@icon('search')</button>
                        <button v-if="searching" v-cloak class="search-box-cancel text-neg" v-on:click="clearSearch" type="button">@icon('close')</button>
                    </form>
                    <h6 class="text-muted">{{ trans_choice('entities.search_total_results_found', $totalResults, ['count' => $totalResults]) }}</h6>
                    <div class="book-contents">
                        @include('partials.entity-list', ['entities' => $entities, 'showPath' => true])
                    </div>
                    @if($hasNextPage)
                        <div class="text-right mt-m">
                            <a href="{{ $nextPageLink }}" class="button outline">{{ trans('entities.search_more') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@stop
