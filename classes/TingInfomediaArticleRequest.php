<?php
class TingInfomediaArticleRequest extends TingGenericInfomediaRequest {
  protected $action = 'getArticleRequest';
  protected $resultClass = 'TingInfomediaArticleResult';


  public function getArticle() {
    $this->action = 'getArticleRequest';
  }

  public function checkArticle() {
    $this->action = 'checkArticleRequest';
  }

  public function setFaust($id) {
    $this->setParameter('articleIdentifier', array('faust' => $id));
  }

}
