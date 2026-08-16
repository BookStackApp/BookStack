<?php

namespace Tests\View;

use BookStack\View\ViewBlockDefaults;
use Tests\TestCase;

class ViewBlockDefaultsTest extends TestCase
{
    public function test_each_has_a_unique_id_and_non_key_label()
    {
        $locations = ViewBlockDefaults::getLocations();
        $classes = [];
        $trueById = [];

        foreach ($locations as $location) {
            $blocksByPosition = ViewBlockDefaults::getForLocation($location);
            foreach ($blocksByPosition as $blocks) {
                array_push($classes, ...$blocks);
            }
        }

        $blocks = array_unique($classes);
        foreach ($blocks as $block) {
            $id = $block::getId();
            $this->assertArrayNotHasKey($id, $trueById);
            $trueById[$id] = true;

            $label = $block::getLabel();
            $this->assertIsString($label);
            $this->assertDoesNotMatchRegularExpression('/^[a-z]{1,10}\./', $label);
        }

        $this->assertGreaterThan(10, count($trueById));
    }
}
