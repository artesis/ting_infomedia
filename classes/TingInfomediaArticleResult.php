<?php

class TingInfomediaArticleResult {

  public $type = 'Article';

  protected $xml;

  /**
   * @var TingInfomediaArticleRequest
   */
  protected $request;

  public $error;
  public $parts = array();
  public $length;

  public function __construct($result, TingInfomediaArticleRequest $request) {
    $this->xml = $result;
    $this->request = $request;
    $this->result = new stdClass();
    $this->process();
  }

  protected function process() {
    $dom = new DOMDocument();

    if (!@$dom->loadXML($this->xml)) {
      throw new TingClientException('malformed xml in infomedia-response: '. $this->xml);
    }
    $xpath = new DOMXPath($dom);
    $action = $this->request->getAction();
    $method = substr($action, 0, 3) == 'get' ? 'get' : 'check';

    $responseNode = '/uaim:' . $method . 'ArticleResponse';
    $detailsNode = '/uaim:' . $method . 'ArticleResponseDetails';
    $errorNode = '/uaim:error';

    $nodelist = $xpath->query($responseNode);

    if ($nodelist->length == 0) {
      throw new TingClientException('TingClientInfomediaRequest got no Infomedia response: ' . $this->xml);
    }

    $errorlist = $xpath->query($responseNode . $errorNode);

    if ($errorlist->length > 0) {
      $this->error = $errorlist->item(0)->nodeValue;
      return;
    }

    $detailslist = $xpath->query($responseNode . $detailsNode);
    $this->length = $detailslist->length;
    $identifierlist = $xpath->query($responseNode . $detailsNode . '/uaim:articleIdentifier');
    $verifiedlist = $xpath->query($responseNode . $detailsNode . '/uaim:articleVerified');

    if ($method == 'check') {
      for($i = 0; $i < $detailslist->length; $i++) {
        $identifier = $identifierlist->item($i)->nodeValue;
        $verified = $verifiedlist->item($i)->nodeValue;
        $this->parts[] = array(
          'identifier' => $identifier,
          'verified' => strcasecmp('true', $verified) == 0,
        );
      }
    }
    else {
      $articlelist = $xpath->query($responseNode . $detailsNode . '/uaim:imArticle');
      for($i = 0; $i < $detailslist->length; $i++) {
        $identifier = $identifierlist->item($i)->nodeValue;
        $verified = $verifiedlist->item($i)->nodeValue;
        if ($verified != "false") {
          $article = $articlelist->item($i)->nodeValue;
        }
        else {
          $article = $verifiedlist->item($i)->nodeValue;
        }
        $this->parts[] = array(
          'identifier' => $identifier,
          'verified' => strcasecmp('true', $verified) == 0,
          'article' => $article,
        );
      }
    }
  }

}
