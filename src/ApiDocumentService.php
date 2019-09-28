<?php
/**
 * Created by PhpStorm.
 * User: Antonin
 * Date: 10/09/2019
 * Time: 22:36
 */

namespace N1n1du44\ClientApiDocumentBundle;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiDocumentService
{
  private $apiDocumentCommunicationService;

  /**
   * ApiDocumentService constructor.
   * @param ApiDocumentCommunicationService $apiDocumentCommunicationService
   */
  public function __construct(ApiDocumentCommunicationService $apiDocumentCommunicationService)
  {
    $this->apiDocumentCommunicationService = $apiDocumentCommunicationService;
  }

  /**
   * @param $localPath
   * @param $format
   * @return mixed
   * @throws ClientExceptionInterface
   * @throws RedirectionExceptionInterface
   * @throws ServerExceptionInterface
   * @throws TransportExceptionInterface
   */
  public function addLocalFile($localPath, $format)
  {
    $postData = [
      'local_path' => $localPath,
      'format' => $format,
      'login' => $this->apiDocumentCommunicationService->getLogin(),
      'password' => $this->apiDocumentCommunicationService->getPassword()
    ];

    $result = $this->apiDocumentCommunicationService->sendPostRequest('/add-local-document', $postData);
    if ($result['http_code'] == 201) {
      return $result['content']->document_id;
    } else {
      return false;
    }
  }

  public function getOcr($documentId) {
    $url = '/documents/' . $documentId . '/ocr';
    $resultat = $this->apiDocumentCommunicationService->sendGetRequest($url);
    
    if ($resultat['http_code'] == 200) {
      return $resultat['content'];
    } else {
      return false;
    }
  }

  /**
   * @param $text
   * @param $codeTypeDocument
   * @return mixed
   */
  public function getInformationsFromText($text, $codeTypeDocument)
  {
    $postData = [
      'text' => $text,
      'code_type_document' => $codeTypeDocument,
      'login' => $this->apiDocumentCommunicationService->getLogin(),
      'password' => $this->apiDocumentCommunicationService->getPassword()
    ];

    $result = $this->apiDocumentCommunicationService->sendPostRequest('/get-informations-from-text', $postData);
    if ($result['http_code'] == 200) {
      return $result['content'];
    } else {
      return false;
    }
  }


}