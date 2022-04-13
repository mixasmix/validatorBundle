<?php

namespace Mixasmix\ValidationBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MixasmixValidationBundleExtension extends Extension
{
    private const BUNDLE_NAME = 'fingineers_validation';

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter(self::BUNDLE_NAME . '.url', $config['url']);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return self::BUNDLE_NAME;
    }
}
