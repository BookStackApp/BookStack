@extends('base')

@section('head')
    <script src="/libs/tinymce/tinymce.min.js?ver=4.3.7"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex">
        <form action="{{$page->getUrl()}}" data-page-id="{{ $page->id }}" method="POST" class="flex flex-fill">
            @if(!isset($isDraft))
                <input type="hidden" name="_method" value="PUT">
            @endif
            @include('pages/form', ['model' => $page])
        </form>

        <div class="floating-toolbox" ng-controller="PageAttributeController" page-id="{{ $page->id or 0 }}">
            <form ng-submit="saveAttributes()">
                <table>
                    <tr ng-repeat="attribute in attributes">
                        <td><input type="text" ng-model="attribute.name" ng-change="attributeChange(attribute)" ng-blur="attributeBlur(attribute)" placeholder="Attribute Name"></td>
                        <td><input type="text" ng-model="attribute.value" ng-change="attributeChange(attribute)" ng-blur="attributeBlur(attribute)" placeholder="Value"></td>
                    </tr>
                </table>
                <button class="button pos" type="submit">Save attributes</button>
            </form>
        </div>

    </div>
    @include('partials/image-manager', ['imageType' => 'gallery', 'uploaded_to' => $page->id])

@stop