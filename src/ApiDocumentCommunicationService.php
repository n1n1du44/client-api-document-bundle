<?php
/**
 * Created by PhpStorm.
 * User: antonin
 * Date: 08/05/2019
 * Time: 10:30
 */

namespace N1n1du44\ClientApiDocumentBundle;


use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiDocumentCommunicationService
{

  private $jwtToken;
  private $login;
  private $password;
  private $urlApi;

  public function __construct($apiUrl, $apiLogin, $apiPassword)
  {
    $this->jwtToken = null;
//    $this->urlApi = "http://api-documents.fr/";
//    $this->login = "admin";
//    $this->password = 'admin';
    $this->urlApi = $apiUrl;
    $this->login = $apiLogin;
    $this->password = $apiPassword;
  }

  /**
   * @param $url
   * @param $post
   * @param bool $retryMode
   * @return array
   * @throws TransportExceptionInterface
   * @throws ClientExceptionInterface
   * @throws RedirectionExceptionInterface
   * @throws ServerExceptionInterface
   */
  public function sendPostRequest($url, $post, $retryMode =false) {
    $requestResult = [
      'http_code' => 0,
      'content' => null,
      'error' => false,
      'error_message' => null
    ];

    if (is_null($this->jwtToken) || $retryMode) {
      if (!$this->authenticate()) {
        $requestResult['error'] = true;
        $requestResult['error_message'] = "Génération du JWT impossible";
      }
    }


    $target_url = $this->urlApi . $url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: multipart/form-data',
      'Authorization: Bearer ' . $this->jwtToken
    ));
    curl_setopt($ch, CURLOPT_URL,$target_url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);

    $requestResult = [
      'http_code' => $httpcode,
      'content' => json_decode($response)
    ];


    return $requestResult;
  }

  /**
   * @param $url
   * @param bool $retryMode
   * @return array|bool
   * @throws TransportExceptionInterface
   * @throws ClientExceptionInterface
   * @throws RedirectionExceptionInterface
   * @throws ServerExceptionInterface
   */
  public function sendGetRequest($url, $retryMode = false) {
    $completeUrl = $this->urlApi . $url;
    $requestResult = [
      'http_code' => 0,
      'content' => null,
      'error' => false,
      'error_message' => null,
      'url' => $completeUrl
    ];

    if (is_null($this->jwtToken) || $retryMode) {
      if (!$this->authenticate()) {
        $requestResult['error'] = true;
        $requestResult['error_message'] = "Génération du JWT impossible";
      }
    }

    $httpClient = new CurlHttpClient(array('auth_bearer' => $this->jwtToken));
    try {
      $response = $httpClient->request("GET", $completeUrl);
      $requestResult['http_code'] = $response->getStatusCode();
      if ($requestResult['http_code'] == 201 || $requestResult['http_code'] == 200) {
        $requestResult['content'] = json_decode($response->getContent(), true );
      } else if ($requestResult['http_code'] == 401) {
        $requestResult = $this->sendGetRequest($url, true);
      }

    } catch (TransportExceptionInterface $e) {
      $requestResult['error'] = true;
      $requestResult['error_message'] = "erreur http client";
    }

    return $requestResult;
  }


  /**
   * @return bool
   * @throws TransportExceptionInterface
   * @throws ClientExceptionInterface
   * @throws RedirectionExceptionInterface
   * @throws ServerExceptionInterface
   */
  public function authenticate() {

      $globalUrl = $this->urlApi . '/login_check';
    $httpClient = HttpClient::create();
    $response = $httpClient->request('POST', $globalUrl, [
      'json' => ['username' => $this->login,'password' => $this->password]
    ]);

    // gets the HTTP status code of the response
    $statusCode = $response->getStatusCode();

    if ($statusCode == 200) {
      $content = $response->getContent();
      $data = json_decode($content);
      $this->jwtToken = $data->token;

      return true;
    } else {
      return false;
    }
  }

  /**
   * @return string
   */
  public function getLogin(): string
  {
    return $this->login;
  }

  /**
   * @return string
   */
  public function getPassword(): string
  {
    return $this->password;
  }


}
