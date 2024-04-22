<?php

namespace Wubbleyou\AccessControlChecker\ACCRules;

use Illuminate\Routing\Route;
use Tests\TestCase;

class MiddlewareRule extends ACCRule {
    public string $name = 'MiddlewareRule';

    public function __construct(public array $middleware, public bool $strict = false) {}

    public function handle(Route $route, TestCase $test, string $routeName): array
    {
        $errors = [];
        $currentMiddleware = $route->gatherMiddleware();
        $routeName = $route->getName() ?? $route->getActionName();

        $matches = array_intersect($this->middleware, $currentMiddleware);

        if(count($matches) < count($this->middleware)) {
            $errors[] = $this->getName() . " - {$routeName} should have middleware [" . implode(',', $this->middleware) . "] but does not [" . implode(',', $currentMiddleware) . ']';
        }

        if($this->strict && count($matches) !== count($currentMiddleware)) {
            $errors[] = $this->getName() . " - {$routeName} should have only this middleware present [" . implode(',', $this->middleware) . "] but has [" . implode(',', $currentMiddleware) . ']';
        }

        return $errors;
    }
}