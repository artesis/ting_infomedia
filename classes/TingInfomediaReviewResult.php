<?php

class TingInfomediaReviewResult {

  public $type = 'Review';

  protected $xml;

  /**
   * @var TingInfomediaReviewRequest
   */
  protected $request;

  public $error;

  public $parts = array();

  public $length;

  public function __construct($result, TingInfomediaReviewRequest $request) {
    $this->xml = $result;
    $this->request = $request;
    $this->result = new stdClass();
    $this->process();
  }

  protected function process() {
    $dom = new DOMDocument();
    $dom->loadXML($this->xml);
    $xpath = new DOMXPath($dom);
    $action = $this->request->getAction();
    $method = substr($action, 0, 3) == 'get' ? 'get' : 'check';

    $responseNode = '/uaim:' . $method . 'ReviewResponse';
    $detailsNode = '/uaim:' . $method . 'ReviewResponseDetails';
    $errorNode = '/uaim:error';
    $articleNode = '/uaim:imArticle';
    $nodelist = $xpath->query($responseNode);

    if ($nodelist->length == 0) return;
    #throw new TingClientException('TingClientInfomediaRequest got no Infomedia response: ', $responseString);


    $errorlist = $xpath->query($responseNode . $errorNode);

    if ($errorlist->length > 0) {
      $this->error = $errorlist->item(0)->nodeValue;
      return;
    }

    $detailslist = $xpath->query($responseNode . $detailsNode);
    $result->length = $detailslist->length;
    $identifierlist = $xpath->query($responseNode . $detailsNode . '/uaim:workIdentifier');
    $countlist = $xpath->query($responseNode . $detailsNode . '/uaim:reviewsCount');

    if ($this->method == 'check') {
      for($i = 0; $i < $detailslist->length; $i++) {
        $identifier = $identifierlist->item($i)->nodeValue;
        $count = $countlist->item($i)->nodeValue;
        $this->parts[] = array('identifier' => $identifier,'count' => (int) $count);
      }
    }
    else {
      for($i = 0; $i < $detailslist->length; $i++) {
        $identifier = $identifierlist->item($i)->nodeValue;
        $count = $countlist->item($i)->nodeValue;
        $identifiers = $xpath->query('uaim:articleIdentifier', $detailslist->item($i));
        $articleidentifiers = array();

        for($j = 0; $j < $identifiers->length; $j++) {
          $articleidentifiers[] = $identifiers->item($j)->nodeValue;
        }

        $articlelist = $xpath->query('uaim:imArticle', $detailslist->item($i));
        $articles = array();

        for($j = 0; $j < $articlelist->length; $j++) {
          $articles[] = $articlelist->item($j)->nodeValue;
        }

        $this->parts[] = array(
          'identifier' => $identifier,
          'count' => (int) $count,
          'identifier_list' => $articleidentifiers,
          'article_list' => $articles,
        );
      }
    }
  }

}