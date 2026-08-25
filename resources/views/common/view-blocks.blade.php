{{--
$location - string
$position - string
--}}
@php
    $sectionVars = get_defined_vars();
@endphp
@foreach($viewBlocks->getInstancesForLocationAndPositionForCurrentUser($location, $position) as $block)
    @include($block->getView($sectionVars), $block->withData($sectionVars))
@endforeach