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
use Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_slt_comments&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="comment-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'comment')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'comment', Text::_('COM_SLT_COMMENTS_TITLE_COMMENT', true)); ?>
    <div class="row">
        <div class="col-lg-9">
            <div>
                <fieldset class="adminform">
	                <?php echo $this->form->renderField('name_author'); ?>
	                <?php echo $this->form->renderField('comment'); ?>
                </fieldset>
            </div>
        </div>
        <div class="col-lg-3">
	        <?php echo $this->form->renderField('state'); ?>
	        <?php //echo $this->form->renderField('created_by'); ?>
	        <?php //echo $this->form->renderField('modified_by'); ?>
	        <?php echo $this->form->renderField('date_creation'); ?>


	        <?php echo $this->form->renderField('id_content'); ?>
	        <?php echo $this->form->renderField('id_parent'); ?>
	        <?php if ($this->state->params->get('save_history', 1)) : ?>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
                </div>
	        <?php endif; ?>
        </div>
    </div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
