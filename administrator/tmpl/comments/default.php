<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Slt_comments
 * @author     Mitriy_Bug <info@codersite.ru>
 * @copyright  2025 Mitriy_Bug
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('com_slt_comments.admin')
    ->useScript('com_slt_comments.admin');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_slt_comments');

$saveOrder = $listOrder == 'a.ordering';

if (!empty($saveOrder))
{
	$saveOrderingUrl = 'index.php?option=com_slt_comments&task=comments.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}
//dd($this->items);
// Получаем названия статей
if (!empty($this->items)) {
	$db = Factory::getContainer()->get(DatabaseDriver::class);

	$contentIds = [];
	foreach ($this->items as $item) {
		if (!empty($item->id_content)) {
			$contentIds[] = (int)$item->id_content;
		}
	}
	if (!empty($contentIds)) {
		// Убираем дубликаты
		$contentIds = array_unique($contentIds);

		$query = $db->getQuery(true);
		$query->select($db->quoteName(['id', 'title']))
			->from($db->quoteName('#__content'))
			->where($db->quoteName('id') . ' IN (' . implode(',', $contentIds) . ')');
		$db->setQuery($query);

		try {
			$articles = $db->loadObjectList('id');
			foreach ($this->items as &$item) {
				if (!empty($item->id_content) && isset($articles[$item->id_content])) {
					$item->content_title = $articles[$item->id_content]->title;
				} else {
					$item->content_title = 'Имя статьи не найдено';
				}
			}
			unset($item);
		} catch (Exception $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
}
?>

<form action="<?php echo Route::_('index.php?option=com_slt_comments&view=comments'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

				<div class="clearfix"></div>
				<table class="table table-striped" id="commentList">
					<thead>
					<tr>
						<th class="w-1 text-center">
							<input type="checkbox" autocomplete="off" class="form-check-input" name="checkall-toggle" value=""
								   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						
					<?php if (isset($this->items[0]->ordering)): ?>
					<th scope="col" class="w-1 text-center d-none d-md-table-cell">
					    <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<?php endif; ?>

					<th  scope="col" class="w-1 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
                        <th scope="col">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_SLT_COMMENTS_FIELD_AUTHOR_LABEL', 'a.name_author', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_SLT_COMMENTS_TITLE_COMMENTS', 'a.comment', $listDirn, $listOrder); ?>
                        </th>
					<th scope="col" class="w-3 d-none d-lg-table-cell" >
						<?php echo HTMLHelper::_('searchtools.sort',  'COM_SLT_COMMENTS_COMMENTS_DATE_CREATION', 'a.date_creation', $listDirn, $listOrder); ?>
                    </th>
                        <th scope="col" class="w-3 d-none d-lg-table-cell" >
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_SLT_COMMENTS_ID_CONTENT', 'a.id_content', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="w-3 d-none d-lg-table-cell" >
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_SLT_COMMENTS_COMMENTS_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody <?php if (!empty($saveOrder)) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" <?php endif; ?>>
					<?php foreach ($this->items as $i => $item) :
                        //dd($item);
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate  = $user->authorise('core.create', 'com_slt_comments');
						$canEdit    = $user->authorise('core.edit', 'com_slt_comments');
						$canCheckin = $user->authorise('core.manage', 'com_slt_comments');
						$canChange  = $user->authorise('core.edit.state', 'com_slt_comments');
						?>
						<tr class="row<?php echo $i % 2; ?>" data-draggable-group='1' data-transition>
							<td class="text-center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							
							<?php if (isset($this->items[0]->ordering)) : ?>

							<td class="text-center d-none d-md-table-cell">

							<?php

							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
							}							?>							<span class="sortable-handler<?php echo $iconClass ?>">
							<span class="icon-ellipsis-v" aria-hidden="true"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
								<?php endif; ?>
							</td>
							<?php endif; ?>

							
							<td class="text-center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'comments.', $canChange, 'cb'); ?>
							</td>

                            <td>
                                <a href="<?php echo Route::_('index.php?option=com_slt_comments&task=comment.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->name_author ?: Text::_('COM_SLT_COMMENTS_NO_TITLE')); ?>
                                </a>
                            </td>
                            <td>
								<?php
								$shortText = HTMLHelper::_('string.truncate', strip_tags($this->escape($item->comment)), 100, true, false);
                                echo $shortText;
                                ?>
                            </td>

							<td class="d-none d-lg-table-cell">
								<?php echo $item->date_creation; ?>
							</td>

                            <td class="d-none d-lg-table-cell">
								<?php $link = Route::_('index.php?option=com_content&task=article.edit&id=' . (int)$item->id_content); ?>
                                    <?php
                                    if (!empty($item->content_title)) {?>
	                                <?php $link = Route::_('index.php?option=com_content&task=article.edit&id=' . (int)$item->id_content); ?>
                                    	<a href="<?php echo $link; ?>" target="_blank" class="d-flex gap-2">
	                                    <?php 
	                                    	echo HTMLHelper::_('string.truncate', strip_tags($item->content_title), 30, true, false);
                                        ?>
                                        </a>
                                    <?php
                                    } else { 
                                    echo 'материал не выбран'; 
                                	}
                                     ?>
                            </td>
                            <td class="d-none d-lg-table-cell">
								<?php echo $item->id; ?>
                            </td>

						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>