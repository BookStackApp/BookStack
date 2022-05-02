<!DOCTYPE html>
<html lang="{{ config('app.lang') }}"
      dir="{{ config('app.rtl') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
</head>
<body>
    <div id="loginredirect-wrapper" style="display:none">
        @include('auth.parts.login-form-' . $authMethod)
    </div>

    <script nonce="{{ $cspNonce }}">
        window.onload = function(){document.forms['login-form'].submit()};
    </script>
</body>
</html>
