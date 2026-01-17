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

$id = $displayData->id ?? '';
$itemId = $displayData->id_content ?? '';
$parentId = $displayData->id_parent ?? 0;
$name = $displayData->name_author ?? 'Аноним';
$date = Factory::getDate($displayData->date_creation)->format('d.m.y, H:i') ?? '';
$comment = $displayData->comment;
$likes = $displayData->likes ?? 0;
$dislikes = $displayData->dislikes ?? 0;
$isModerate = (bool)($displayData->isModerate ?? false);
?>
<div class="slt-comment-item-name fw-bold"><?php echo $name. ($isModerate ? ' (на модерации)' : ''); ?></div>
<div class="slt-comment-item-date"><?php echo $date; ?></div>
<div class="slt-comment-item-text mb-3"><?php echo $comment; ?></div>
<?php if (!$isModerate) : ?>
<div class="slt-comment__reaction d-flex gap-3">
    <button type="button" class="btn-comment__answer" data-parentid="<?php echo $id; ?>" data-articleid="<?php echo (int)$itemId; ?>">
        Ответить
    </button>
    <div class="blog-comment__like-like d-flex gap-2">
        <button class="slt-comment-item-btn-like btn btn-link p-0 text-decoration-none" type="button" data-id="<?php echo $id; ?>" data-type="like">👍</button>
        <div class="slt-comment__like-count"><?php if ($likes > 0) echo $likes; ?></div>
    </div>
    <div class="blog-comment__like-dislike d-flex gap-2">
        <button class="slt-comment-item-btn-like btn btn-link p-0 text-decoration-none" type="button" data-id="<?php echo $id; ?>" data-type="dislike">👎</button>
        <div class="slt-comment__dislike-count"><?php if ($dislikes > 0) echo $dislikes; ?></div>
    </div>
</div>
<?php endif; ?>
