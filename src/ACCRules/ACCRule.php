<?php

namespace Wubbleyou\AccessControlChecker\ACCRules;

use Illuminate\Routing\Route;
use Tests\TestCase;

class ACCRule implements IACCRule {
    public string $name = 'ACCRule';

    public function handle(Route $route, TestCase $test, string $routeName): array
    {
        return [];
    }

    public function setName(string $name): ACCRule
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
}