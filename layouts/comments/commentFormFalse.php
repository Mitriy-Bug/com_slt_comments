<?php
/**
 * @package     SltComments
 * @subpackage  layouts
 * @author      Mitriy_Bug <info@codersite.ru>
 * @copyright   2025 Mitriy_Bug
 * @license     GNU General Public License v2 or later
 * @var         $displayData array
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();
$lang = $app->getLanguage();
$lang->load('com_slt_comments', JPATH_SITE); // Загружаем языковой файл

$articleId = $displayData['articleID'] ?? 0;
$parentId = $displayData['parentId'] ?? 0;
$limitComment = $displayData['limitComment'] ?? 1000;
$uid = $displayData['uid'] ?? '';
$showPolicy = $displayData['textPolicy'] ?? 0;
?>
		<form name="commentForm" class="form-validate">
			<div class="slt-comments-form-message alert p-0" role="alert"></div>
			<fieldset>
					<div class="row mb-3">
						<div class="col-md-6">
							<div class="form-group">
								<label for="name_author" class="form-label required">
									<?php echo Text::_('COM_SLT_COMMENTS_FIELD_NAME_LABEL'); ?>
								</label>
								<input
									type="text"
									name="name_author"
									class="form-control required"
									maxlength="100"
									required
									aria-required="true"
									placeholder="<?php echo Text::_('COM_SLT_COMMENTS_FIELD_NAME_LABEL'); ?>" />
							</div>
						</div>
					</div>
				<div class="form-group mb-3">
					<label for="comment" class="form-label required">
						<?php echo Text::_('COM_SLT_COMMENTS_FIELD_COMMENT_LABEL'); ?>
					</label>
					<textarea
						name="comment"
						class="form-control required"
						rows="5"
						maxlength="100"
						required
						aria-required="true"
						placeholder="<?php echo Text::_('COM_SLT_COMMENTS_COMMENT_PLACEHOLDER'); ?>">
                    </textarea>
				</div>
                <input type="hidden" name="content_item_id" value="<?php echo (int) $articleId; ?>" />
                <input type="hidden" name="parent_id" value="<?php echo (int) $parentId; ?>" />
                <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                <input type="hidden" name="show_policy" value="<?php echo $showPolicy; ?>" />
				<div class="d-grid gap-2">
					<button type="button" class="btn btn-primary btn-lg">
						<?php echo Text::_('COM_SLT_COMMENTS_SUBMIT_BUTTON'); ?>
					</button>
				</div>
			</fieldset>
		</form>
