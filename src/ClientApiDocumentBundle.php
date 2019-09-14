<?php


namespace N1n1du44\ClientApiDocumentBundle;


use N1n1du44\ClientApiDocumentBundle\DependencyInjection\ClientApiDocumentExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ClientApiDocumentBundle extends Bundle
{
  public function getContainerExtension()
  {
    if (null === $this->extension) {
      $this->extension = new ClientApiDocumentExtension();
    }

    return $this->extension;
  }

}