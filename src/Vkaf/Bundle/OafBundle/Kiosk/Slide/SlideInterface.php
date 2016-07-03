<?php

namespace Vkaf\Bundle\OafBundle\Kiosk\Slide;

use Symfony\Component\HttpFoundation\Response;

interface SlideInterface
{
    /**
     * Renders a slide.
     *
     * @param array $options
     * @param array $next
     *
     * @return Response|array
     */
    public function render($options, $next);
}
