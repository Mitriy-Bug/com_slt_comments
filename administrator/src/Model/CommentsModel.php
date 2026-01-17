<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Slt_comments
 * @author     Mitriy_Bug <info@codersite.ru>
 * @copyright  2025 Mitriy_Bug
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

namespace Slt\Component\Slt_comments\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseDriver;

/**
 * Methods supporting a list of Comments records.
 *
 * @since  1.0.0
 */
class CommentsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'name_author', 'a.name_author',
				'comment', 'a.comment',
				'search',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'a.id', $direction = 'DESC')
	{
		// Получаем состояние фильтра поиска
		$search = $this->getUserStateFromRequest(
			$this->context . '.filter.search',
			'filter_search',
			'',
			'string'
		);
		$this->setState('filter.search', $search);

		// Получаем состояние фильтра состояния
		$published = $this->getUserStateFromRequest(
			$this->context . '.filter.state',
			'filter_state',
			'',
			'string'
		);
		$this->setState('filter.state', $published);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = Factory::getContainer()->get(DatabaseDriver::class);
		$query = $db->getQuery(true);

		// Выбираем все поля из таблицы комментариев
		$query->select('a.*')
			->from($db->quoteName('#__slt_comments', 'a'));

		// Фильтр: поиск
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				// Поиск по ID
				$searchId = (int) substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = ' . $searchId);
			} else {
				// Поиск по тексту в нескольких полях
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$searchConditions = [
					$db->quoteName('a.name_author') . ' LIKE ' . $search,
					$db->quoteName('a.comment') . ' LIKE ' . $search,
					$db->quoteName('a.date_creation') . ' LIKE ' . $search,
				];
				$query->where('(' . implode(' OR ', $searchConditions) . ')');
			}
		}

		// Фильтр: состояние (опубликовано/не опубликовано)
		$state = $this->getState('filter.state');

		if ($state !== '' && $state !== '*') {
			$query->where($db->quoteName('a.state') . ' = ' . (int) $state);
		}

		// Сортировка
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		}

		return $query;
	}
}
