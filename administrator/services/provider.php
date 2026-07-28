<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jugendtraining
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Jugendtraining\Component\Jugendtraining\Administrator\Extension\JugendtrainingComponent;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->registerServiceProvider(
            new ComponentDispatcherFactory('\\Jugendtraining\\Component\\Jugendtraining')
        );

        $container->registerServiceProvider(
            new MVCFactory('\\Jugendtraining\\Component\\Jugendtraining')
        );

        $container->set(
            ComponentInterface::class,
            static function (Container $container): ComponentInterface {
                $component = new JugendtrainingComponent(
                    $container->get(ComponentDispatcherFactoryInterface::class)
                );

                $component->setMVCFactory(
                    $container->get(MVCFactoryInterface::class)
                );

                return $component;
            }
        );
    }
};
