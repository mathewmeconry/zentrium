<?php

namespace Zentrium\Bundle\CoreBundle\Csrf;

use Dunglas\AngularCsrfBundle\Routing\RouteMatcher as BaseRouteMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class RouteMatcher extends BaseRouteMatcher
{
    private $router;
    private $optionsRoute;

    public function __construct(RouterInterface $router, $optionsRoute)
    {
        $this->router = $router;
        $this->optionsRoute = $optionsRoute;
    }

    public function match(Request $request, array $routes)
    {
        $currentRoute = $request->get('_route');
        foreach ($routes as &$route) {
            if (!isset($route['route']) || $route['route'] !== $this->optionsRoute) {
                continue;
            }
            $protect = $this->router->getRouteCollection()->get($currentRoute)->getOption('protect');
            if ($protect) {
                $route['route'] = '^'.$currentRoute.'$';
            } else {
                $route['route'] = '^$';
            }
        }
        unset($route);

        return parent::match($request, $routes);
    }
}
