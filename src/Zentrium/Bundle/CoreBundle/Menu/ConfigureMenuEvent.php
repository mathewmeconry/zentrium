<?php

namespace Zentrium\Bundle\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    private $factory;
    private $menu;
    private $options;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface    $menu
     * @param array                      $options
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, array $options)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->options = $options;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
