<?php
/**
 * @package     Reddesign.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrderingUrl = 'index.php?option=com_reddesign&task=cliparts.saveOrderAjax&tmpl=component';
$disableClassName = '';
$disabledLabel = '';
$config = ReddesignEntityConfig::getInstance();
$clipartPreviewWidth = $config->getMaxClipartPreviewWidth();
$clipartPreviewHeight = $config->getMaxClipartPreviewHeight();

if ($listOrder == 'c.ordering')
{
	JHtml::_('rsortablelist.sortable', 'clipartsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
else
{
	$disabledLabel = JText::_('COM_REDDESIGN_COMMON_ORDERING_DISABLED');
	$disableClassName = 'inactive tip-top';
}
?>
<form action="index.php?option=com_reddesign&view=cliparts" method="post" id="adminForm" name="adminForm">
	<?php echo JText::_('COM_REDDESIGN_COMMON_FILTER'); ?>
	<?php
		echo RLayoutHelper::render(
			'searchtools.default',
			array(
				'view' => $this,
				'options' => array(
					'filterButton' => true,
					'searchField' => 'search_cliparts',
					'searchFieldSelector' => '#filter_search_cliparts',
					'limitFieldSelector' => '#list_search_cliparts',
					'activeOrder' => $listOrder,
					'activeDirection' => $listDirn
				)
			)
		);
	?>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDDESIGN_COMMON_NOTHING_TO_DISPLAY') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table id="clipartsList" class="table table-striped">
			<thead>
				<tr>
					<th style="width:5%;">
						<?php echo JText::_('COM_REDDESIGN_COMMON_NUM'); ?>
					</th>
					<th class="nowrap center hidden-phone" style="width:5%;">
						<?php echo JHtml::_('rsearchtools.sort', '', 'c.ordering', $listDirn, $listOrder, null, 'asc', '', 'icon-sort'); ?>
					</th>
					<th style="width:5%;">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>)"? />
					</th>
					<th>
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_CLIPART_NAME', 'c.name', $listDirn, $listOrder);?>
					</th>
					<th>
						<?php echo JText::_('COM_REDDESIGN_CLIPART_PREVIEW');?>
					</th>
					<th>
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_CLIPART_CATEGORY', 'categoryName', $listDirn, $listOrder);?>
					</th>
					<th style="width:5%;">
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_COMMON_ENABLED', 'c.state', $listDirn, $listOrder);?>
					</th>
					<th style="width:5%;">
						<?php echo JHtml::_('rsearchtools.sort', 'COM_REDDESIGN_COMMON_ID', 'c.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $row) : ?>
					<tr>
						<td>
							<?php echo $i + 1; ?>
						</td>
						<td class="order nowrap center hidden-phone">
							<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
								<i class="icon-ellipsis-vertical"></i>
							</span>
							<input type="text"
								   style="display:none"
								   name="order[]"
								   size="5"
								   value="<?php echo $row->ordering; ?>"
								   class="width-20 text-area-order "
								>
							<span class="sortable-handler <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
								<i class="icon-ellipsis-vertical"></i>
							</span>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
							<?php echo JHtml::_('link', JRoute::_('index.php?option=com_reddesign&task=clipart.edit&id=' . $row->id), $row->name); ?>
						</td>
						<td>
							<div class="pull-left thumbnail">
								<object
									id="clipart<?php echo $row->id; ?>"
									class="thumbnailSVG"
									data="<?php echo JURI::root() . 'media/com_reddesign/cliparts/' . $row->clipartFile; ?>"
									type="image/svg+xml">
								</object>
							</div>
						</td>
						<td>
							<?php echo $row->categoryName; ?>
						</td>
						<td>
							<?php echo JHtml::_('rgrid.published', $row->state, $i, 'cliparts.', true, 'cb'); ?>
						</td>
						<td>
							<?php echo $row->id;?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	jQuery('.thumbnailSVG').each(function () {

		var svgThumbnail = document.getElementById(jQuery(this).attr('id'));
		svgThumbnail.addEventListener("load", function() {
			setSVGElementScale(this);
		});

		// Some elements are already loaded
		setSVGElementScale(svgThumbnail);
	});

	function setSVGElementScale(svgDocument)
	{
		if (svgDocument && typeof(svgDocument) != "undefined")
		{
			var svgDocumentContent = svgDocument.contentDocument;
			if (svgDocumentContent && typeof(svgDocumentContent) != "undefined")
			{
				var svgElementInner = jQuery(svgDocumentContent.documentElement);
				if (typeof(svgElementInner) != "undefined")
				{
					jQuery(svgElementInner).attr("height", <?php echo $clipartPreviewHeight; ?>);
					jQuery(svgElementInner).attr("width", <?php echo $clipartPreviewWidth; ?>);
					jQuery(svgElementInner).attr("preserveAspectRatio", "xMidYMid meet");
				}
			}
		}
	}
</script>
