<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional\App;

use Dmytrof\ModelsManagementBundle\DmytrofModelsManagementBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DmytrofModelsManagementBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/dmytrof_models_management_bundle_tests/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/dmytrof_models_management_bundle_tests/logs';
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');

        // ORM 3 needs lazy proxies: native lazy objects on PHP 8.4+, otherwise Symfony LazyGhost
        // (symfony/var-exporter < 8; version 8 removed LazyGhost and requires PHP 8.4 anyway)
        $container->loadFromExtension('doctrine', [
            'orm' => \PHP_VERSION_ID >= 80400
                ? ['enable_native_lazy_objects' => true]
                : ['enable_lazy_ghost_objects' => true],
        ]);
    }
}
