<?php

// Overwrite configuration that can interfere with the phpstan/larastan scanning.
config()->set([
    'filesystems.default' => 'local',
]);
