<?php
/**
 * @package        RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class TableRSForm_PayFast
 */
class TableRSForm_PayFast extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $submission_id = null;
	/**
	 * @var null
	 */
	public $form_id = null;
	/**
	 * @var string
	 */
	public $signature = '';

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_payfast', 'submission_id', $db);
	}

}