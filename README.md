# Wubbleyou Boundaries
Boundaries is a DX tool used to generate unit tests to automatically test your routes against developer-determined rules.

Two rules are provided with out of the box with Boundaries, a `MiddlewareRule` and a `PolicyRule`

## Installation
Install the package normally via Composer

```
composer require wubbleyou/boundaries
```

## Usage
To get started you'll need to run the following command to generate the base test file:
```
php/sail artisan wubbleyou:generate-test
```

## Configuration
Now you've got your test generated and saved in `tests\Feature\Boundaries\BoundaryTest.php` you might notice a trait has been placed in `app\Traits\BoundaryRouteTrait.php` which contains 2 methods, please read the additional information at the bottom to understand why you need to supply these.

### getWhitelist()
The `getWhitelist()` method allows you to return an array of all the routes you want to be ignored by Wubbleyou\Boundaries.

```
return [
    'login',
    'register',
    'homepage',
    'about',
];
```

### getRoutes()
The `getRoutes()` method allows you to return an array to specify the exact assertions that should be ran on each route, here's an example:

```
$admin = User::factory()->create(['is_admin', true]);
$userOne = User::factory()->create();
$userTwo = User::factory()->create();

return [
    'users.change-password' => [
        new MiddlewareRule(['web', 'auth', 'incorrect']),
        new PolicyRule('get', 403, $userOne, ['user' => $userTwo]),
    ]
];
```

#### MiddlewareRule
The middleware rule tests your route against a certain set of middleware, if it doesn't match all of these middleware the test will fail.

MiddlewareRule also has an optional second parameter for `$strict` (default: `false`). If strict is enabled the supplied array of expected middleware must match exactly to the middleware present on the route. If strict is not enabled the middleware present on the route must contain all the expected middleware but can also contain other middleware we're not testing for.

```
new MiddlewareRule(['web', 'auth'], true),
```

#### PolicyRule
The policy rule performs a test against a specific route to test the HTTP status code. You supply it with:

- The request type (get/post/etc)
- The expected HTTP status code response (200, 404, 403, etc)
- A user to test the route as (this can be set to NULL to test unauthenticated) (default: null)
- Parameters the route expects (optional) (default: [])

And it will test that route and return an error if the response code doesn't match the one you've supplied.

#### Custom BoundaryRules
You can also generate custom BoundaryRules using the following command:

```
php/sail artisan wubbleyou:generate-rule RuleName
```

An example BoundaryRule would look like:

```
<?php

namespace App\BoundaryRules;

use Wubbleyou\Boundaries\BoundaryRules\BoundaryRule;
use Illuminate\Routing\Route;
use Tests\TestCase;

class TestingRule extends BoundaryRule {
    public function handle(Route $route, TestCase $test, string $routeName): array
    {
        return ['This test failed because this array is not empty'];
    }
}
```

#### Closures
You can also supply a Closure as a BoundaryRule instead:

```
function(Route $route) {
    if($route->getName() !== 'hello') {
        return ['Route name is not hello'];
    }

    return [];
},
```

#### Additional Information
Boundaries testing will fail if every route is not accounted for in either `getWhitelist()` or `getRoutes()`, if you want to generate a list of routes you that aren't in either of those run the following command:

```
php/sail artisan wubbleyou:missing-routes
```

Please note this command requires you to have generated the BoundaryRouteTrait to function, if you need to generate just the trait without any of the tests you can run:

```
php/sail artisan wubbleyou:generate-route-trait
```