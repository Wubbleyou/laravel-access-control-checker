<?php

namespace Wubbleyou\AccessControlChecker\Tests;

use Tests\TestCase;

use Wubbleyou\AccessControlChecker\Helpers\RouteListGeneration;
use Wubbleyou\AccessControlChecker\ACCRules\ACCRule;
use Illuminate\Support\Facades\Route;

class BaseACCTest extends TestCase
{
    /**
     * Tests that our route checker will run on
     *
     * @return void
     */
    public function testACCRoutes(): void
    {
        $missingRoutes = RouteListGeneration::findMissingRoutes($this->testableRoutes(), $this->ignoredRoutes());
        $this->assertEquals([], $missingRoutes, 'The following routes are not being tested and must be added to your route trait file (app/Traits/ACCRouteTrait.php): ' . json_encode($missingRoutes));
    }

    /**
     * Test all routes against the provided ACCRules.
     * @return void
     */
    public function testACCRules(): void
    {
        $routes = $this->testableRoutes();
        $errors = [];

        foreach ($routes as $routeName => $rules) {
            $route = Route::getRoutes()->getByName($routeName);

            if ($route === null) {
                $errors[] = "$routeName does not exist, it is probably not registered by the Laravel app - was is removed?";
            } else {
                foreach($rules as $rule) {
                    if($rule instanceof \Closure) {
                        $errors = array_merge($errors, $rule($route, $this, $routeName));
                    } else if($rule instanceof ACCRule) {
                        $errors = array_merge($errors, $rule->handle($route, $this, $routeName));
                    }
                }
            }
        }

        $errorCount = count($errors);
        $this->assertEmpty($errors, "The following routes ({$errorCount}) failed ACC rules:\n\n" . implode("\n", $errors) . "\n");
    }
}