<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class WmtsLayer extends Layer
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string")
     */
    protected $capabilitiesUrl;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string")
     */
    protected $layerId;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="json_deflate")
     */
    protected $capabilities;

    public function __construct()
    {
        parent::__construct();
    }

    public function getCapabilitiesUrl()
    {
        return $this->capabilitiesUrl;
    }

    public function setCapabilitiesUrl($capabilitiesUrl)
    {
        $this->capabilitiesUrl = $capabilitiesUrl;

        return $this;
    }

    public function getLayerId()
    {
        return $this->layerId;
    }

    public function setLayerId($layerId)
    {
        $this->layerId = $layerId;

        return $this;
    }

    public function getCapabilities()
    {
        return $this->capabilities;
    }

    public function setCapabilities($capabilities)
    {
        $this->capabilities = $capabilities;

        return $this;
    }
}
