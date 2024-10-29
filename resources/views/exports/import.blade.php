@extends('layouts.simple')

@section('body')

    <div class="container small">

        <main class="card content-wrap auto-height mt-xxl">
            <h1 class="list-heading">{{ trans('entities.import') }}</h1>
            <form action="{{ url('/import') }}" method="POST">
                {{ csrf_field() }}
                <div class="flex-container-row justify-space-between wrap gap-x-xl gap-y-s">
                    <p class="flex min-width-l text-muted mb-s">
                        Import content using a portable zip export from the same, or a different, instance.
                        Select a ZIP file to import then press "Validate Import" to proceed.
                        After the file has been uploaded and validated you'll be able to configure & confirm the import in the next view.
                    </p>
                    <div class="flex-none min-width-l flex-container-row justify-flex-end">
                        <div class="mb-m">
                            <label for="file">Select ZIP file to upload</label>
                            <input type="file"
                                   accept=".zip,application/zip,application/x-zip-compressed"
                                   name="file"
                                   id="file"
                                   class="custom-simple-file-input">
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ url('/books') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('entities.import_validate') }}</button>
                </div>
            </form>
        </main>
    </div>

@stop
