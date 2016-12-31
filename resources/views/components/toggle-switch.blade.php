<div toggle-switch="{{$name}}" class="toggle-switch @if($value) active @endif">
    <input type="hidden" name="{{$name}}" value="{{$value?'true':'false'}}"/>
    <div class="switch-handle"></div>
</div>
<script>
    (function() {
       var toggle = document.querySelector('[toggle-switch="{{$name}}"]');
       var toggleInput = toggle.querySelector('input');
       toggle.onclick = function(event) {
           var checked = toggleInput.value !== 'true';
           toggleInput.value = checked ? 'true' : 'false';
           checked ? toggle.classList.add('active') : toggle.classList.remove('active');
       };
    })()
</script>