<?php

namespace Wubbleyou\AccessControlChecker\Helpers;

use Illuminate\Support\Facades\Route;

class RouteListGeneration
{
    public static function getRoutes(): array
    {
        $routeCollection = collect(Route::getRoutes());

        return $routeCollection->map(function($route) {
            $routeName = self::getFullPrefixedNameFromRoute($route);

            return [
                'name' => $routeName,
                'action' => $route->getAction()['uses'],
            ];
        })->toArray();
    }

    public static function findMissingRoutes($accountedRoutes, $ignoredRoutes): array
    {
        $routesToCheck = self::getRoutes();
        
        $nameCol = array_column($routesToCheck, 'name');
        $actionCol = array_column($routesToCheck, 'action');

        foreach ($accountedRoutes as $route) {
            $key = array_search($route, $nameCol, true);
            if ($key !== false) {
                unset($routesToCheck[$key]);
            }
        }

        foreach ($ignoredRoutes as $ignored) {
            $key = array_search($ignored, $nameCol, true);
            if ($key !== false) {
                unset($routesToCheck[$key]);
                continue;
            }

            $key = array_search($ignored, $actionCol, true);
            if ($key !== false) {
                unset($routesToCheck[$key]);
            }
        }

        foreach ($routesToCheck as $key => $route) {
            if(is_callable($route['action'])) {
                unset($routesToCheck[$key]);
            }
        }

        return $routesToCheck;
    }

    public static function getFullPrefixedNameFromRoute(\Illuminate\Routing\Route $route): string
    {
        $prefix = null;
        return ($prefix ? $prefix . '.' : '') . $route->getName();
    }
}