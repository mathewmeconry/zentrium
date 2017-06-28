<?php

namespace Vkaf\Bundle\OafBundle\Kiosk\Slide;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class SlideManager
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var array
     */
    private $types;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
        $this->types = [];
    }

    /**
     * Registers a new slide type.
     *
     * @param string         $type
     * @param SlideInterface $slide
     */
    public function registerType($type, SlideInterface $slide)
    {
        $this->types[$type] = $slide;
    }

    /**
     * Checks whether a given slide type is available.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasType($type)
    {
        return isset($this->types[$type]);
    }

    /**
     * Renders a slide.
     *
     * @param string $type
     * @param array  $options
     * @param array  $next
     *
     * @return Response
     *
     * @throws RenderException
     */
    public function render($type, $options, $next)
    {
        if (!$this->hasType($type)) {
            throw new RenderException(sprintf('Unknown type "%s".', $type));
        }

        $response = $this->types[$type]->render($options, $next);
        if (is_array($response)) {
            $response = new Response($this->templating->render('VkafOafBundle:Kiosk:'.Inflector::camelize($type).'.html.twig', $response));
        }

        return $response;
    }
}
