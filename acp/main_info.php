<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\acp;

class main_info
{
  function module()
  {
    return array(
      'filename' => '\podnapisi\connector\acp\main_module',
      'title'    => 'ACP_CONNECTOR_TITLE',
      'version'  => '0.0.1',
      'modes'    => array(
        'settings' => array('title' => 'ACP_CONNECTOR', 'auth' => 'ext_podnapisi/connector && acl_a_board', 'cat' => array('ACP_CONNECTOR_TITLE')),
      ),
    );
  }
}
