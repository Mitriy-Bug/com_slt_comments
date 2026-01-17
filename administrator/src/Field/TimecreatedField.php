<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Slt_comments
 * @author     Mitriy_Bug <info@codersite.ru>
 * @copyright  2025 Mitriy_Bug
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

namespace Slt\Component\Slt_comments\Administrator\Field;

defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Form\FormField;
use \Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Supports an HTML select list of categories
 *
 * @since  1.0.0
 */
class TimecreatedField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'timecreated';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since   1.0.0
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		$time_created = $this->value;

		// If time is empty or invalid, use current time in UTC for saving
		if (empty($time_created) || $time_created === '0000-00-00 00:00:00' || !strtotime($time_created)) {
			$now = Factory::getDate(); // UTC
			$time_created = $now->toSql(true);
		}

		// Store raw UTC date in hidden input
		$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($time_created, ENT_QUOTES, 'UTF-8') . '" />';


		$hidden = (boolean) $this->element['hidden'];

		if ($hidden == null || !$hidden)
		{
			$pretty_date = HTMLHelper::_('date', $time_created, Text::_('DATE_FORMAT_LC2'), true);
			$html[]      = "<div>" . $pretty_date . "</div>";
		}

		return implode($html);
	}
}
