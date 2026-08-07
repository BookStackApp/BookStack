{{--
$location - string
$position - string
--}}
@php
    $sectionVars = get_defined_vars();
    $sectionRequest = request();
@endphp
@foreach($viewBlocks->getForLocation($location, $position) as $section)
    @include($section->getView(), $section->withData($sectionVars, $sectionRequest))
@endforeach