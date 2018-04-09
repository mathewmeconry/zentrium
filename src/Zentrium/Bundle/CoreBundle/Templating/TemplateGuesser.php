<?php

namespace Zentrium\Bundle\CoreBundle\Templating;

use Doctrine\Common\Util\ClassUtils;
use InvalidArgumentException;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Templating\TemplateGuesser as BaseTemplateGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateGuesser extends BaseTemplateGuesser
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel, []);

        $this->kernel = $kernel;
    }

    public function guessTemplateName($controller, Request $request)
    {
        if (!is_array($controller)) {
            throw new InvalidArgumentException('First argument must be an array.');
        }

        $className = ClassUtils::getClass($controller[0]);
        if (!preg_match('/Controller\\\(.+)Controller$/', $className, $classMatch)) {
            throw new InvalidArgumentException('The class does not look like a controller class.');
        }

        $action = preg_replace('/Action$/', '', $controller[1]);

        $bundleName = $this->getBundleForClass($className);

        return sprintf(($bundleName ? '@'.$bundleName.'/' : '').$classMatch[1].'/'.$action.'.'.$request->getRequestFormat().'.twig');
    }

    private function getBundleForClass($class)
    {
        $reflectionClass = new ReflectionClass($class);
        $bundles = $this->kernel->getBundles();

        do {
            $namespace = $reflectionClass->getNamespaceName();
            foreach ($bundles as $bundle) {
                if ($bundle->getNamespace() === 'Symfony\Bundle\FrameworkBundle') {
                    continue;
                }
                if (strpos($namespace, $bundle->getNamespace()) === 0) {
                    return preg_replace('/Bundle$/', '', $bundle->getName());
                }
            }
            $reflectionClass = $reflectionClass->getParentClass();
        } while ($reflectionClass);
    }
}
