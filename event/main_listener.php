<?php
/**
 *
 * @package phpBB Extension - Podnapisi Connector
 * @copyright (c) 2015 Gregor Kališnik
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace podnapisi\connector\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{

  static public function getSubscribedEvents()
  {
    return array(
      'core.user_setup' => 'load_language_on_setup',
    );
  }

  /* @var \phpbb\user */
  protected $user;

  /**
   * Constructor
   *
   * @param \phpbb\user $user User object
   */
  public function __construct(\phpbb\user $user)
  {
    $this->user = $user;
  }

  public function load_language_on_setup($event)
  {
    $lang_set_ext = $event['lang_set_ext'];
    $lang_set_ext[] = array(
      'ext_name' => 'podnapisi/connector',
      'lang_set' => 'common',
    );

    $event['lang_set_ext'] = $lang_set_ext;
  }
}
