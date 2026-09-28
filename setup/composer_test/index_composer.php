<?php
// setup/composer_test/index_composer.php

require_once __DIR__ . '/vendor/autoload.php';

use TestApp\Greeter;

$greeter = new Greeter();
echo $greeter->greet("Lập Trình Viên") . "\n";
