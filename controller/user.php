<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\controller;

class user extends base {

  /* @var \phpbb\config\config */
  protected $config;
  /* @var \phpbb\request\request */
  protected $request;
  /* @var \phpbb\db\driver\factory */
  protected $db;
  /* @var \phpbb\auth\auth */
  protected $auth;
  /* @var \phpbb\auth\provider_collection */
  protected $auth_provider;

  protected function _fetch_user($user_id)
  {
    // TODO any other, better way, to do this?
    $sql = 'SELECT *
            FROM ' . USERS_TABLE . "
            WHERE user_id = " . $this->db->sql_escape($user_id);
    $result = $this->db->sql_query($sql, 500);
    $data = $this->db->sql_fetchrow($result);
    $this->db->sql_freeresult($result);

    if (!isset($data['user_id'])) {
      // Make sure there are no suprises with empty arrays
      $data = array();
    }

    return $data;
  }

  /**
   * Constructor
   *
   * @param \phpbb\config\config $config
   * @param \phpbb\request\request $request
   * @param \phpbb\db\driver\factory $db
   */
  public function __construct(\phpbb\config\config $config, \phpbb\request\request $request,
                              \phpbb\db\driver\factory $db, \phpbb\auth\auth $auth,
                              \phpbb\auth\provider_collection $auth_provider)
  {
    $this->config = $config;
    $this->request = $request;
    $this->db = $db;
    $this->auth = $auth;
    $this->auth_provider = $auth_provider;
  }

  /**
   * REST API for user resources
   *
   * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
   */
  public function handle($user_id)
  {
    $user_id = (int) $user_id;

    $this->_validate_request(array('GET', 'POST'));

    switch ($this->request->server('REQUEST_METHOD')) {
      case 'GET':
        $data = array(
          'status' => 'ok',
          'data'   => $this->_fetch_user($user_id)
        );
        break;

      case 'POST':
        $attributes = $this->request->variable_names(\phpbb\request\request_interface::POST);
        $data = array();
        $safe_attrs = explode(',', $this->config['podnapisi_connector_user_safe_attributes']);
        foreach ($attributes as $attribute) {
          // Use only attributes that are allowed to be changed
          if (in_array($attribute, $safe_attrs))
            $data['user_' . $attribute] = $this->request->variable($attribute, '', \phpbb\request\request_interface::POST);
        }
        if (empty($data)) {
          $data = array('status' => 'no-data');
        } else {
          // TODO Per field sanitization? Error reporting?
          $sql = 'UPDATE ' . USERS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . ' WHERE user_id = ' . $user_id;
          $this->db->sql_query($sql);

          $data = array('status' => 'ok');
        }
        break;
    }

    return $this->_make_response($data);
  }

  /**
   * REST API for testing user membership
   *
   * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
   */
  public function handle_member($user_id, $group)
  {
    $user_id = (int) $user_id;
    if (is_numeric($group))
      $group = (int) $group;
    else
      $group = $this->db->sql_escape($group);

    $this->_validate_request(array('GET'));

    if (is_int($group)) {
      $q = "SELECT ug.group_id ".
           "FROM ". USER_GROUP_TABLE ." ug ".
           "WHERE ug.user_id = $user_id ".
             "AND ug.group_id = $group ".
             "AND ug.user_pending = 0 ".
           "LIMIT 1";
    } else {
      $q = "SELECT ug.group_id ".
           "FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g ".
           "WHERE ug.user_id = $user_id ".
             "AND g.group_name = '$group' ".
             "AND ug.group_id = g.group_id ".
             "AND ug.user_pending = 0 ".
           "LIMIT 1";
    }

    $r = $this->db->sql_query($q, 500);
    $data = $this->db->sql_fetchrow($r);
    $this->db->sql_freeresult($r);

    return $this->_make_response(array(
      'status' => $data ? 'ok' : 'not-member'
    ));
  }

  /**
   * REST API for user ACL
   *
   * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
   */
  public function handle_acl($user_id)
  {
    $user_id = (int) $user_id;

    $this->_validate_request(array('GET'));

    $options = $this->request->variable('options', '');
    if (empty($options))
      return $this->_make_response(array('status' => 'no-options'));

    // TODO Sanitization?
    $options = explode(',', $options);

    // TODO Error handling?
    $user = $this->_fetch_user($user_id);
    if (empty($user))
      return $this->_make_response(array('status' => 'no-user'));

    $this->auth->acl($user);

    $data = array(
      'status' => $this->auth->acl_gets($options) ? 'ok' : 'failed',
    );

    return $this->_make_response($data);
  }

  /**
   * REST API for user authentication
   *
   * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
   */
  public function handle_auth()
  {
    $this->_validate_request(array('POST'));

    $username = $this->request->variable('username', '', \phpbb\request\request_interface::POST);
    $password = $this->request->variable('password', '', \phpbb\request\request_interface::POST);

    $provider = $this->auth_provider->get_provider();
    if ($provider) {
      $login = $provider->login($username, $password);

      if ($login['status'] == LOGIN_SUCCESS) {
        $data = array(
          'status' => 'ok',
          'user'   => $login['user_row'],
        );
      } else {
        $data = array(
          'status' => 'login_failed',
        );
      }
    } else {
      $data = array(
        'status' => 'no_provider',
      );
    }

    return $this->_make_response($data);
  }
}
