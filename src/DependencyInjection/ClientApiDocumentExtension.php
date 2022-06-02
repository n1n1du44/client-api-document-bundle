<?php


namespace N1n1du44\ClientApiDocumentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ClientApiDocumentExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container)
  {
//    var_dump($configs); die;
    $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    $loader->load('services.xml');

    $configuration = $this->getConfiguration($configs, $container);
    $config = $this->processConfiguration($configuration, $configs);

    $definition = $container->getDefinition('n1n1du44_client_api_document.api_document_communication_service');
    $definition->setArgument(0, $config['api_document_url']);
    $definition->setArgument(1, $config['api_document_login']);
    $definition->setArgument(2, $config['api_document_password']);
  }

  public function getAlias() : string
  {
    return 'client_api_document';
  }

}
