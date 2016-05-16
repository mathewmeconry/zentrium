<?php

namespace Zentrium\Bundle\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ColorToTextTransformer implements DataTransformerInterface
{
    public function transform($color)
    {
        if (null === $color) {
            return '';
        }

        return $color;
    }

    public function reverseTransform($text)
    {
        if (!$text) {
            return;
        }

        $color = strtolower($text);
        if (!preg_match('/^#[a-f0-9]{6}$/', $color)) {
            throw new TransformationFailedException('Invalid color.');
        }

        return $color;
    }
}
