<?php

namespace Wubbleyou\AccessControlChecker\Rules;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PolicyRule extends ACCRule {
    public string $name = 'PolicyRule';

    public function __construct(public string $reqType, public int $expectedStatus, public $user = null, public array $params = []) {}

    public function handle(Route $route, TestCase $test, string $routeName): array
    {
        $errors = [];
        $reqType = $this->reqType;

        if($this->user) {
            $testCase = $test->actingAs($this->user, 'web')
                ->$reqType(route($routeName, $this->params));

            // WEIRD: We have to log out here to not break the next test where we're expecting a guest user?
            Auth::logout();
        } else {
            $testCase = $test->$reqType(route($routeName, $this->params));
        }

        if ($this->expectedStatus !== $testCase->getStatusCode()) {
            $userName = ($this->user) ? $this->user->email : 'Guest user';
            $errors[] = $this->getName() . " - {$reqType}:{$routeName} does not match for {$userName} - Expected {$this->expectedStatus}, got {$testCase->getStatusCode()}";
        }

        return $errors;
    }
}