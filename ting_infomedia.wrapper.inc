<?php
/**
 * @file
 * Infomedia helper functions.
 */

module_load_include('client.inc', 'ting');

/**
 * Infomedia - retrieve article identified by faust
 *
 * @param int $id Faust
 * @return TingInfomediaArticleResult
 */
function ting_infomedia_get_article($id) {
  $request = ting_get_request_factory('infomediaArticle');

  if (empty($request)) {
    return NULL;
  }

  $request->getArticle();
  $request->setFaust($id);
  $request = ting_infomedia_add_user_creds($request);

  return ting_execute($request);
}

/**
 * Infomedia - verify that request of item identify by faust does return an article
 *
 * @param int $id Faust
 * @return TingInfomediaArticleResult
 */
function ting_infomedia_check_article($id) {
  $request = ting_get_request_factory('infomediaArticle');

  if (empty($request)) {
    return NULL;
  }

  $request->checkArticle();
  $request->setFaust($id);
  $request = ting_infomedia_add_user_creds($request);

  return ting_execute($request);
}

/**
 * Infomedia - retrieve reviews for an item identified by faust or ISBN
 *
 * @param int $id
 *   Faust or ISBN
 * @param bool $id_is_isbn
 *   Is $id an ISBN instead of faust
 * @return TingInfomediaArticleResult
 */
function ting_infomedia_get_review($id, $id_is_isbn = FALSE) {
  $request = ting_get_request_factory('infomediaReview');

  if (empty($request)) {
    return NULL;
  }

  $request->getReview();

  if ($id_is_isbn) {
    $request->setISBN($id);
  }
  else {
    $request->setFaust($id);
  }

  $request = ting_infomedia_add_user_creds($request);
  return ting_execute($request);
}

function checkUser() {
  return TRUE;
  // TODO implement
}

/**
 * Infomedia - count how many reviews an item identified by faust or ISBN has
 *
 * @param int $id
 *   Faust or ISBN
 * @param bool $id_is_isbn
 *   Is $id an ISBN instead of faust
 * @return TingInfomediaArticleResult
 */
function ting_infomedia_check_review($id, $id_is_isbn = FALSE) {
  $request = ting_get_request_factory('infomediaReview');

  if (empty($request)) {
    return NULL;
  }

  $request->checkReview();

  if ($id_is_isbn) {
    $request->setISBN($id);
  }
  else {
    $request->setFaust($id);
  }

  $request = ting_infomedia_add_user_creds($request);
  return ting_execute($request);
}

/**
 * Helper function used by infomedia wrapper functions
 *
 * Adds agency and if user is authorized user credentials to request object and
 * returns the object
 *
 * @param TingGenericInfomediaRequest $request
 *   Request object to add user credentials to
 * @return TingGenericInfomediaRequest
 */
function ting_infomedia_add_user_creds(TingGenericInfomediaRequest $request) {
  try {
    $creds = ding_user_get_creds();
    if (isset($creds['library_card_number'])) {
      $userid = $creds['library_card_number'];
    }
    else {
      $userid = $creds['name'];
    }
    $request->setUser($userid);
    $request->setPin($creds['pass']);
  }
  catch (DingProviderAuthException $e) {
    //do nothing
  }

  $name = isset($creds['name']) ? $creds['name'] : '';
  $pass = isset($creds['pass']) ? $creds['pass'] : '';

  // check if user is authenticated
  ding_provider_invoke_page('user', 'authenticate', $name, $pass);
  return $request;
}

