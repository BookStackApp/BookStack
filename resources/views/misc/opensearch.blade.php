@php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; @endphp
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName>{{ mb_strimwidth(setting('app-name'), 0, 16) }}</ShortName>
  <Description>{{ trans('common.opensearch_description', ['appName' => setting('app-name')]) }}</Description>
  <Image width="256" height="256" type="image/png">{{ setting('app-icon') ?: url('/icon.png') }}</Image>
  <Image width="180" height="180" type="image/png">{{ setting('app-icon-180') ?: url('/icon-180.png') }}</Image>
  <Image width="128" height="128" type="image/png">{{ setting('app-icon-128') ?: url('/icon-128.png') }}</Image>
  <Image width="64" height="64" type="image/png">{{ setting('app-icon-64') ?: url('/icon-64.png') }}</Image>
  <Image width="32" height="32" type="image/png">{{ setting('app-icon-32') ?: url('/icon-32.png') }}</Image>
  <Url type="text/html" rel="results" template="{{ url('/search') }}?term={searchTerms}"/>
  <Url type="application/opensearchdescription+xml" rel="self" template="{{ url('/opensearch.xml') }}"/>
</OpenSearchDescription>
