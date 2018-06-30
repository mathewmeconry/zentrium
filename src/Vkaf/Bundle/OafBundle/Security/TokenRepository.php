<?php

namespace Vkaf\Bundle\OafBundle\Security;

interface TokenRepository
{
    public function findOneByToken(string $token);
}
