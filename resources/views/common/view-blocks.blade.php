{{--
$location - string
$position - string
--}}
@php
    $sectionVars = get_defined_vars();
@endphp
@foreach($viewBlocks->getForLocation($location, $position) as $section)
    @include($section->getView($sectionVars), $section->withData($sectionVars))
@endforeach