@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-8 faded">
        <div class="breadcrumbs">
            <a href="{{ baseUrl('/books') }}" class="text-button">@icon('book'){{ trans('entities.books') }}</a>
            <span class="sep">&raquo;</span>
            <a href="{{ baseUrl('/create-book') }}" class="text-button">@icon('add'){{ trans('entities.books_create') }}</a>
            <a href="{{ baseUrl('/create-book') }}" class="text-button">@icon('add'){{ trans('entities.books_import') }}</a>
        </div>
    </div>
@stop

@section('body')

<div class="container small">
    <p>&nbsp;</p>
    <div class="card">
        <h3>@icon('add') {{ trans('entities.books_import') }}</h3>
        <div class="body">
            <h4> General import </h4>
            Import book XML file works automatically across many platforms.
            <form action="{{ baseUrl("/books/import") }}" method="POST" enctype="multipart/form-data">
              {{ csrf_field() }}
              Select a file: <input type="file" name="genericXML" required><br />
              <input type="submit" value="Go"></input>
            </form>
            <h4> Specific import </h4>
            Works with Evernote, Wiki, and Checkstyle.<br /><br />

            <form action="{{ baseUrl("/books/import") }}" method="POST" enctype="multipart/form-data">
              {{ csrf_field() }}
              <select name="media">
                  <option>Wiki</option>
                  <option>Evernote</option>
                  <option>Checkstyle</option>
              </select><br /><br />

              Select a file: <input type="file" name="providerXML" required><br />
              <input type="submit" value="Go"></input>
            </form>
        </div>
    </div>
</div>
<p class="margin-top large"><br></p>
    @include('components.image-manager', ['imageType' => 'cover'])
@stop
