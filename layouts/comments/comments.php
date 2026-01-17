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
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();
$lang = $app->getLanguage();
$lang->load('com_slt_comments', JPATH_SITE); // Загружаем языковой файл

$commentsArray = $displayData['comments'] ?? [];

$resultArrayComments = [];
if (!empty($commentsArray)){
    foreach ($commentsArray as $item) {
	    $resultArrayComments[$item->id_parent][] = $item;
    }
}
$cookie = $app->input->cookie;
$uid = $cookie->getString('SLT_COOKIE_UID');
$isModerate = false;

$countComments = $displayData['countActiveComments'] ?? '';
$formData = $displayData['formData'] ?? [];
?>
<?php if (!empty($resultArrayComments) && !empty($resultArrayComments[0])) : ?>
<div class="slt-comments-list">
    <h3><?php echo Text::_('PLG_CONTENT_SLT_COMMENTS_TITLE'); ?><?php echo !empty($countComments) ? ' <span>('.$countComments.')</span>' : ''; ?></h3>
    <div class="slt-comments-form-outer mt-4">
        <?php echo LayoutHelper::render('components.slt_comments.comments.commentFormFalse', $formData); ?>
    </div>
    <div class="slt-comments-list-inner">
        <div class="slt-comments-tree">
            <?php displayCommentsRecursive($resultArrayComments,$uid); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
function displayCommentsRecursive($commentsArray,$uid = null, $parentId = 0, $level = 0): void
{
    if (!empty($commentsArray[$parentId])) :
        foreach ($commentsArray[$parentId] as $comment) :
            $isModerate = false;
            if (!empty($uid) && $uid == $comment->uid && $comment->state == 0) {
                $isModerate = true;
                $comment->isModerate = true;
            }
            $marginLeft = $level * 30;
            ?>
            <div class="slt-comment-item<?php echo $level > 0 ? ' slt-comment-reply mt-3' : ''; ?><?php echo $isModerate ? ' moderate text-bg-warning' : ''; ?> card mb-3 p-3"
                 style="margin-left: <?php echo $marginLeft; ?>px;">
                <?php echo LayoutHelper::render('components.slt_comments.comments.commentBlock', $comment); ?>
                <?php if (!$isModerate) : ?>
                    <div class="slt-comment__answers-list">
                        <div class="slt-comment__answers-list-form"></div>
                        <?php displayCommentsRecursive($commentsArray, $uid, $comment->id, $level + 1);?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach;
    endif;
}
?>
