<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\controller;

use \Symfony\Component\HttpFoundation;

class session extends base {

  /* @var \phpbb\config\config */
  protected $config;
  /* @var \phpbb\request\request */
  protected $request;
  /* @var \phpbb\db\driver\factory  */
  protected $db;

  /**
   * Constructor
   *
   * @param \phpbb\config\config $config
   * @param \phpbb\request\request $request
   * @param \phpbb\db\driver\factory $db
   */
  public function __construct(\phpbb\config\config $config, \phpbb\request\request $request, \phpbb\db\driver\factory $db)
  {
    $this->config = $config;
    $this->request = $request;
    $this->db = $db;
  }

  /**
  * REST API for session resources
  *
  * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
  */
  public function handle($session_id)
  {
    $this->_validate_request(array('GET'));

    // Copied from phpbb/session.php:session_begin
    // TODO any other, better way, to do this?
    $sql = 'SELECT u.*, s.*
            FROM ' . SESSIONS_TABLE . ' s, ' . USERS_TABLE . " u
            WHERE s.session_id = '" . $this->db->sql_escape($session_id) . "'
                    AND u.user_id = s.session_user_id";
    $result = $this->db->sql_query($sql);
    $data = $this->db->sql_fetchrow($result);
    $this->db->sql_freeresult($result);

    if (!isset($data['user_id'])) {
      // Make sure there are no suprises with empty arrays
      $data = array();
    }

    return new \Symfony\Component\HttpFoundation\Response(json_encode($data));
  }
}
