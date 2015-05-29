<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\controller;

use \Symfony\Component\HttpKernel\Exception\HttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class base {

  /**
   * Validates the incoming request. It needs $config and $request to be set!
   */
  protected function _validate_request($methods = array('GET'))
  {
    $authentication = $this->config['podnapisi_connector_auth_key'];
    if (empty($authentication) || $this->request->variable('auth_key', '') != $authentication)
      throw new NotFoundHttpException("Not found");
    if (!in_array($this->request->server('REQUEST_METHOD'), $methods))
      throw new HttpException(400, "Bad request");
  }

  protected function _make_response($data)
  {
    return new \Symfony\Component\HttpFoundation\Response(json_encode($data));
  }
}
