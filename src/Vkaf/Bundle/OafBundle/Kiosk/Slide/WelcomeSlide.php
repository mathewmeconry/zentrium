<?php

namespace Vkaf\Bundle\OafBundle\Kiosk\Slide;

class WelcomeSlide implements SlideInterface
{
    public function render($options, $next)
    {
        return [
            'message' => isset($options['message']) ? $options['message'] : null,
            'next' => $next,
        ];
    }
}
