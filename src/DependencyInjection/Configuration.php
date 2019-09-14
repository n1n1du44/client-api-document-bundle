<?php


namespace N1n1du44\ClientApiDocumentBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

  /**
   * Generates the configuration tree builder.
   *
   * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
   */
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder('api_client_document');
    $rootNode = $treeBuilder->getRootNode();
    $rootNode
      ->children()
        ->variableNode('api_document_url')->defaultValue('%env(API_DOCUMENT_URL)%')->end()
        ->variableNode('api_document_login')->defaultValue('%env(API_DOCUMENT_LOGIN)%')->end()
        ->variableNode('api_document_password')->defaultValue('%env(API_DOCUMENT_PASSWORD)%')->end();

    return $treeBuilder;
  }
}