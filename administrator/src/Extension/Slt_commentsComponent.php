<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Slt_comments
 * @author     Mitriy_Bug <info@codersite.ru>
 * @copyright  2025 Mitriy_Bug
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */

namespace Slt\Component\Slt_comments\Administrator\Extension;

defined('_JEXEC') or die;

use Slt\Component\Slt_comments\Administrator\Service\Html\SLT_COMMENTS;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Tag\TagServiceTrait;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Categories\CategoryServiceInterface;

/**
 * Component class for Slt_comments
 *
 * @since  1.0.0
 */
class Slt_commentsComponent extends MVCComponent implements RouterServiceInterface, BootableExtensionInterface, CategoryServiceInterface
{
	use AssociationServiceTrait;
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;
	use CategoryServiceTrait, TagServiceTrait {
		CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
		CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
	}

	/** @inheritdoc  */
	public function boot(ContainerInterface $container)
	{
		$db = $container->get('DatabaseDriver');
		$this->getRegistry()->register('slt_comments', new SLT_COMMENTS($db));
	}

	
/**
 * Returns the table for the count items functions for the given section.
	 *
	 * @param   string    The section
	 *
	 * * @return  string|null
	 *
	 * @since   4.0.0
	 */
	    protected function getTableNameForSection(?string $section = null)            
	{
	}
	
	/**
     * Adds Count Items for Category Manager.
     *
     * @param   \stdClass[]  $items    The category objects
     * @param   string       $section  The section
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function countItems(array $items, string $section)
    {
	}
}