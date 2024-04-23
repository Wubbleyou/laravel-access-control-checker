<?php

namespace Wubbleyou\AccessControlChecker\ACCRules;

use Illuminate\Routing\Route;
use Tests\TestCase;

interface IACCRule {
    public function handle(Route $route, TestCase $test, string $routeName): array;
    public function setName(string $name);
    public function getName();
}