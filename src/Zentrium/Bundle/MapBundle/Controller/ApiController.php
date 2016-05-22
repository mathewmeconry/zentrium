<?php

namespace Zentrium\Bundle\MapBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zentrium\Bundle\MapBundle\Entity\Position;

/**
 * @NamePrefix("api_map")
 */
class ApiController extends Controller
{
    /**
     * @Post("/api/map/positions")
     * @Secure(roles="ROLE_MAP_POSITION")
     * @ParamConverter("position", converter="zentrium.request_body")
     * @View
     */
    public function positionAction(Position $position)
    {
        $this->get('zentrium_map.manager.position')->update($position);

        return $position;
    }
}
