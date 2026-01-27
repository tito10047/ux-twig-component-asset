<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tito10047\UX\TwigComponentSdc\Runtime\SdcMetadataRegistry;
use Tito10047\UX\TwigComponentSdc\Service\AssetRegistry;
use Tito10047\UX\TwigComponentSdc\Service\ComponentMetadataResolver;
use Tito10047\UX\TwigComponentSdc\EventListener\AssetResponseListener;
use Tito10047\UX\TwigComponentSdc\EventListener\ComponentRenderListener;
use Tito10047\UX\TwigComponentSdc\EventListener\DevComponentRenderListener;
use Tito10047\UX\TwigComponentSdc\Twig\AssetExtension;
use Symfony\Component\AssetMapper\AssetMapperInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(AssetRegistry::class);

    $services->set(AssetResponseListener::class)
        ->args([
            service(AssetRegistry::class),
            service(AssetMapperInterface::class),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'kernel.response',
            'method' => 'onKernelResponse',
        ]);

    $services->set(ComponentRenderListener::class)
        ->args([
            service(SdcMetadataRegistry::class),
            service(AssetRegistry::class),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'Symfony\UX\TwigComponent\Event\PreCreateForRenderEvent',
            'method' => 'onPreCreate',
        ])
        ->tag('kernel.event_listener', [
            'event' => 'Symfony\UX\TwigComponent\Event\PreRenderEvent',
            'method' => 'onPreRender',
        ]);

    $services->set(DevComponentRenderListener::class)
        ->args([
            service(ComponentMetadataResolver::class),
            service(AssetRegistry::class),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'Symfony\UX\TwigComponent\Event\PreRenderEvent',
            'method' => 'onPreRender',
        ]);

    $services->set(AssetExtension::class)
        ->args(['$placeholder' => '<!-- __UX_TWIG_COMPONENT_ASSETS__ -->'])
        ->tag('twig.extension');

    $services = $container->services();

    //	$services->defaults()
    //		->autowire()
    //		->autoconfigure();
    //
    //	// Symfony si samo vytiahne hodnoty z parametrov kontajnera
    //	$services->load(
    //		'%twig_component_sdc.component_namespace%',
    //		'%twig_component_sdc.component_dir%'
    //	);
};
