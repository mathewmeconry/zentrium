<?php

$version = new SebastianBergmann\Version('0.1', __DIR__);
$container->setParameter('version', $version->getVersion());
