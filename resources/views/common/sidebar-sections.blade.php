{{--
$location - string
$position - string
--}}
@php
    $sectionVars = get_defined_vars();
    $sectionRequest = request();
@endphp
@foreach($sidebar->getSectionsForLocation($location, $position) as $section)
    @include($section->getView(), $section->withData($sectionVars, $sectionRequest))
@endforeach