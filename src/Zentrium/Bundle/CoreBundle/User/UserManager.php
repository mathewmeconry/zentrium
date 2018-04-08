<?php

namespace Zentrium\Bundle\CoreBundle\User;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->getRepository()->findOneByCanonicalEmail($this->getCanonicalFieldsUpdater()->canonicalizeEmail($email));
    }
}
