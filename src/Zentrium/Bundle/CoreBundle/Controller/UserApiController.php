<?php

namespace Zentrium\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @NamePrefix("api_users")
 */
class UserApiController extends FOSRestController
{
    /**
     * @Get("/api/users")
     * @View(serializerGroups={"Default", "list"})
     */
    public function cgetAction()
    {
        $users = $this->get('zentrium.repository.user')->findAll();

        return $users;
    }
}
