@php
    $en = trans('editor', [], 'en');
    $lang = trans('editor');
    $mergedText = [];
    foreach ($en as $key => $value) {
      $mergedText[$value] = $lang[$key] ?? $value;
    }
@endphp
<script nonce="{{ $cspNonce }}">
    window.editor_translations = @json($mergedText);
</script>