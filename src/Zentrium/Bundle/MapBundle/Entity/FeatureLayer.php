<?php

namespace Zentrium\Bundle\MapBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class FeatureLayer extends Layer
{
    /**
     * @var ArrayCollection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="Feature", mappedBy="layer")
     */
    protected $features;

    public function __construct()
    {
        parent::__construct();

        $this->features = new ArrayCollection();
    }

    public function getFeatures()
    {
        return $this->features;
    }

    public function setFeatures($features)
    {
        $this->features = $features;

        return $this;
    }
}
