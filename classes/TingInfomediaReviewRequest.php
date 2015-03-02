<?php
class TingInfomediaReviewRequest extends TingGenericInfomediaRequest {
  protected $action = 'getReviewRequest';
  protected $resultClass = 'TingInfomediaReviewResult';

  public function getReview() {
    $this->action = 'getReviewRequest';
  }

  public function checkReview() {
    $this->action = 'checkReviewRequest';
  }

  public function setFaust($id) {
    $this->setParameter('articleIdentifier', array('faust' => $id));
  }

  public function setISBN($id) {
    $this->setParameter('articleIdentifier', array('isbn' => $id));
  }

}
