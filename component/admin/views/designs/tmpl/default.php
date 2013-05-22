<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();

$hasAjaxOrderingSupport = $this->hasAjaxOrderingSupport();

?>

<div id="designs" class="submenu-container">
	<div class="submenu-link">
		<a href="index.php?option=com_reddesign&view=generalinfo">
			<?php echo JText::_('COM_REDDESING_GENERAL_INFO') ?>
		</a>
	</div>
	<div class="submenu-link">
		<a href="index.php?option=com_reddesign&view=background">
			<?php echo JText::_('COM_REDDESING_BACKGROUND') ?>
		</a>
	</div>
	<div class="submenu-link">
		<a href="index.php?option=com_reddesign&view=designarea">
			<?php echo JText::_('COM_REDDESING_DESIGN_AREA') ?>
		</a>
	</div>
	<div class="submenu-link">
		<a href="index.php?option=com_reddesign&view=fontsizes">
			<?php echo JText::_('COM_REDDESING_FONT_SIZES') ?>
		</a>
	</div>
</div>

<div class="form-container">
	<formid="adminForm" name="adminForm" method="post" action="index.php">
	<input type="hidden" value="com_reddesign" name="option">
	<input type="hidden" value="designs" name="view">
	<input type="hidden" value="browse" name="task">
	<input type="hidden" value="" name="boxchecked">
	<input type="hidden" value="" name="hidemainmenu">
	<input type="hidden" value="id" name="filter_order">
	<input type="hidden" value="DESC" name="filter_order_Dir">
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
	<table id="itemsList" class="table table-striped">
		<thead>
		<tr>
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'JFIELD_ORDERING_LABEL', 'ordering', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
				<?php echo JHTML::_('grid.order', $this->items); ?>
			</th>
			<th width="20">
				<input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle">
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'title', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="9%">
				<?php echo JHTML::_('grid.sort', 'JPUBLISHED', 'enabled', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
		</tr>
		<tr>
			<td colspan="3">
				<input type="text" name="search" id="search"
				       value="<?php echo $this->escape($this->getModel()->getState('search',''));?>"
				       class="input-medium" onchange="document.adminForm.submit();"
				       placeholder="<?php echo JText::_('JGLOBAL_TITLE') ?>"
					/>
				<nobr>
					<button class="btn btn-mini" onclick="this.form.submit();">
						<?php echo JText::_('JSEARCH_FILTER'); ?>
					</button>
					<button class="btn btn-mini" onclick="document.adminForm.search.value='';this.form.submit();">
						<?php echo JText::_('JSEARCH_RESET'); ?>
					</button>
				</nobr>
			</td>
			<td>
				<?php //echo AkeebasubsHelperSelect::published($this->getModel()->getState('enabled',''), 'enabled', array('onchange'=>'this.form.submit();', 'class'=>'input-medium')) ?>
			</td>
		</tr>
		</thead>
		<tbody>
		<?php if($count = count($this->items)): ?>
			<?php $i = -1; $m = 1; ?>
			<?php foreach ($this->items as $item) : ?>
				<?php
				$i++; $m = 1-$m;
				$item->published = $item->enabled;
				$ordering = $this->lists->order == 'ordering';
				?>
				<tr class="<?php echo 'row'.$m; ?>">
					<?php if($hasAjaxOrderingSupport === false): ?>
						<td class="order" align="center">
							<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering ); ?></span>
							<span><?php echo $this->pagination->orderDownIcon( $i, $count, true, 'orderdown', 'Move Down', $ordering ); ?></span>
							<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
						</td>
					<?php endif; ?>
					<?php if($hasAjaxOrderingSupport !== false): ?>
						<td class="order nowrap center hidden-phone">
							<?php if ($this->perms->editstate) :
								$disableClassName = '';
								$disabledLabel	  = '';
								if (!$hasAjaxOrderingSupport['saveOrder']) :
									$disabledLabel    = JText::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
					<i class="icon-menu"></i>
				</span>
								<input type="text" style="display:none"  name="order[]" size="5"
								       value="<?php echo $item->ordering;?>" class="input-mini text-area-order " />
							<?php else : ?>
								<span class="sortable-handler inactive" >
					<i class="icon-menu"></i>
				</span>
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<td align="left">
						<a href="index.php?option=com_akeebasubs&view=customfield&id=<?php echo $item->reddesign_designs_id ?>">
							<strong><?php echo $this->escape(JText::_($item->title)) ?></strong>
						</a>
						<p class="smallsub">
							(<span><?php echo $this->escape($item->slug) ?></span>)
						</p>
					</td>
					<td align="center">
						<?php echo JHTML::_('grid.published', $item, $i); ?>
					</td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="20">
					<?php echo  JText::_('COM_AKEEBASUBS_COMMON_NORECORDS') ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="20">
					<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
				</td>
			</tr>
		</tfoot>
	</table>
	</form>
</div>