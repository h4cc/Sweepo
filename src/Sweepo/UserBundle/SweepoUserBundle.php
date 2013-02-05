<?php

namespace Sweepo\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Sweepo\UserBundle\DependencyInjection\Security\Factory\TwitterFactory;

class SweepoUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new TwitterFactory());
    }
}
