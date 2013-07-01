<?php
/**
 * @package     RedDesign.Component
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die();
FOFTemplateUtils::addJS('media://com_reddesign/assets/js/colorpicker/jscolor.js');
FOFTemplateUtils::addCSS('media:///com_reddesign/assets/css/colorpicker.css');

?>
<script type="text/javascript">
var NOarea = parseInt(<?php echo count($this->areas);?>);
var rDesign = jQuery.noConflict();

/**
 * Function for hide color picker
 */
function HideColorPicker(element,reddesign_area_id)
{
	if(element.value==1)
	{
		akeeba.jQuery("#TrcolorPicker"+reddesign_area_id).hide();
		akeeba.jQuery("#saveAllColorbtn"+reddesign_area_id).show();
	}
	else
	{
		akeeba.jQuery("#TrcolorPicker"+reddesign_area_id).show();
		akeeba.jQuery("#saveAllColorbtn"+reddesign_area_id).hide();
	}
}
/**
 * saves color codes into the DB via AJAX.
 *
 * @param reddesign_area_id
 *
 * @param  update
 */
function addNewcolor(reddesign_area_id, update =0)
{
	var newcolor = true;
	if(update==1)
	{
		var newcolor = false;
	}

	var allowAllColor = akeeba.jQuery("input[name='allcolor"+reddesign_area_id+"']:checked").val();
	if(allowAllColor==1)
	{
		finalColorCode = 1;
	}
	else
	{
		var colorCodes = document.getElementsByName('colour_id'+reddesign_area_id+'[]');
		var arr = [];
		for (var i = 0; i < colorCodes.length ; i++) {
		    var aControl = colorCodes[i].value;
		    arr.push(aControl);
		}
		var finalColorCode = arr.join(",");
		if(newcolor)
		{
			finalColorCode = finalColorCode+",#"+document.getElementById('color_code'+reddesign_area_id).value;
		}
	}
	// For AJAX save
	akeeba.jQuery.ajax({
		url: "<?php echo JURI::base(); ?>index.php?option=com_reddesign&view=area&task=ajaxColorSave&format=raw",
		data: {
			reddesign_area_id:reddesign_area_id,
			color_code: finalColorCode
		},
		type: "post",
		success: function (data) {
			var json = akeeba.jQuery.parseJSON(data);
			if(newcolor)
				addRow(json);
			if(allowAllColor==1)
			{
				removeRow(json);
			}

			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).removeClass();
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).addClass("alert alert-success");
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).html(json.message);
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).fadeIn("slow");
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).fadeOut(3000);
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).show();
		},
		error: function (data) {
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).removeClass();
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).addClass("alert alert-error");
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).html(data);
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).fadeIn("slow");
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).fadeOut(3000);
			akeeba.jQuery("#ajaxMessageColor"+reddesign_area_id).show();
		}
	});
	// End Ajax save
}
function removeRow(colors)
{
	akeeba.jQuery("#extra_table"+colors.reddesign_area_id).html("");
}
function addRow(colors)
{
		var cccode = colors.color_code.split(",");
		var lastColor = cccode.length-1;
		cccode = cccode[lastColor];
		if(cccode!="")
		{
			is_img=0;
			var TDinnerHTML=  '<div class="colorSelector_list" ><div style=" background-color:'+cccode+'" >&nbsp;</div></div>';
		}
		else
		{
			is_img=1;
			var TDinnerHTML=  document.getElementById("image_dis").innerHTML;
		}
		akeeba.jQuery("#extra_table"+colors.reddesign_area_id).append(
			'<tr>' +
				'<td>'+TDinnerHTML+'</td>'+
				'<td><div>'+cccode+'</div><input type="hidden" value="'+cccode+'" class="code_image'+colors.reddesign_area_id+'" name="code_image'+colors.reddesign_area_id+'[]"  id="code_image'+colors.reddesign_area_id+'"><input type="hidden" name="is_image'+colors.reddesign_area_id+'[]" value="'+is_img+'" id="is_image'+colors.reddesign_area_id+'"></td>'+
				'<td><div><input type="hidden" name="colour_id'+colors.reddesign_area_id+'[]" id="colour_id'+colors.reddesign_area_id+'" value="'+cccode+'">'+
			            '<input value="Delete" onclick="deletecolor(this,'+colors.reddesign_area_id+')" class="button" type="button" >'+
				'</div>'+
				'</td>'+
			'</tr>'
		);
}
/**
 * Delete color codes into the DB via AJAX.
 *
 * @param r
 *
 * @param  reddesign_area_id
 */
function deletecolor(r,reddesign_area_id)
{
	var i=r.parentNode.parentNode.parentNode.rowIndex;
	document.getElementById('extra_table'+reddesign_area_id).deleteRow(i);
	// Do AJAX Delete
	addNewcolor(reddesign_area_id, update=1);
	// End
}
</script>

<div class="form-container" >

<?php
foreach ($this->areas as $area)
{
	$colorCode = $area->color_code;

	if ($colorCode == 1 || $colorCode == '1')
	{
		$style = "style='display:none;'";
		$btnstyle = "style='display:block;'";
	}
	else
	{
		$style = "style='display:block;'";
		$btnstyle = "style='display:none;'";
	}

?>
		<div class="control-group">
			<div class="controls">
				<b><?php echo $area->title ?></b>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_USE_ALLCOLOR'); ?>
			</label>
			<div class="controls">
				<?php echo $this->lists['allcolor' . $area->reddesign_area_id];?>
				<input type="hidden" name="areacolor[]" value="<?php echo $area->reddesign_area_id;?>">
				<span class="help-block"><?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_USE_ALLCOLOR_DESC'); ?></span>
			</div>
		</div>
		<div id="saveAllColorbtn<?php echo $area->reddesign_area_id;?>" <?php echo $btnstyle;?>>
			<input name="addvalue<?php echo $area->reddesign_area_id;?>" id="addvalue<?php echo $area->reddesign_area_id;?>" class="button" value="<?php echo JText::_( 'COM_REDDESIGN_DESIGNTYPE_COLOR_ADD_ALL_COLOR')?>" onclick="addNewcolor('<?php echo $area->reddesign_area_id;?>',0);" type="button" >
			<div id="ajaxMessageColorContainer" style="height: 25px; padding-bottom: 11px;">
					<div id="ajaxMessageColor<?php echo $area->reddesign_area_id;?>" style="display: block;">
					</div>
				</div>
			</div>
		<div id="TrcolorPicker<?php echo $area->reddesign_area_id;?>" <?php echo $style;?>>
		<div class="control-group" >
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_TEXT'); ?>
			</label>
			<div class="controls">
				<input class="color" value="" id="color_code<?php echo $area->reddesign_area_id;?>" name="color_code<?php echo $area->reddesign_area_id;?>">&nbsp;&nbsp;<input name="addvalue<?php echo $area->reddesign_area_id;?>" id="addvalue<?php echo $area->reddesign_area_id;?>" class="button" value="<?php echo JText::_( 'COM_REDDESIGN_DESIGNTYPE_COLOR_ADD_COLOR')?>" onclick="addNewcolor('<?php echo $area->reddesign_area_id;?>',0);" type="button" >
			</div>
			<div id="ajaxMessageColorContainer" style="height: 25px; padding-bottom: 11px;">
				<div id="ajaxMessageColor<?php echo $area->reddesign_area_id;?>" style="display: block;">
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label ">
				<?php echo JText::_('COM_REDDESIGN_DESIGNTYPE_COLOR_ALLOWED_COLOR'); ?>
			</label>
			<div class="controls">
			<input type="hidden" name="catalog_image<?php echo $area->reddesign_area_id;?>" id="catalog_image<?php echo $area->reddesign_area_id;?>" value=""  >
			<input type="hidden" name="total_extra<?php echo $area->reddesign_area_id;?>" id="total_extra<?php echo $area->reddesign_area_id;?>" value="0" >
			<div id="loadcolors">
			<table class="loadcolor" id="extra_table<?php echo $area->reddesign_area_id;?>" cellpadding="2" cellspacing="2">
				<?php
				if (@$this->lists["color_" . $area->reddesign_area_id] != "1" )
				{
					if (strpos(@$this->lists["color_" . $area->reddesign_area_id], "#") !== false)
					{
						$colorData = explode(",", $this->lists["color_" . $area->reddesign_area_id]);

						for ($j = 0;$j < count($colorData); $j++)
						{
						?>
							<tr valign="top" class="color">
							<td>
								<div class="colorSelector_list">
									<div style="background-color:<?php echo $colorData[$j]?>">&nbsp;</div>
								</div>
							</td>
							<td>
								<div><?php echo $colorData[$j] ?></div>
							</td>
							<td>
								<div>
									<input type="hidden" name="is_image<?php echo $area->reddesign_area_id?>[]" value="0" id="is_image'.$area->reddesign_area_id.'">
									<input type="hidden" class="code_image<?php echo $area->reddesign_area_id?>" name="code_image<?php echo $area->reddesign_area_id?>[]" value="<?php echo $colorData[$j] ?>" id="code_image<?php echo $area->reddesign_area_id?>">
									<input value="Delete" onclick="deletecolor(this,<?php echo$area->reddesign_area_id?>)" class="button" type="button" />
									<input type="hidden" name="colour_id<?php echo $area->reddesign_area_id?>[]" id="colour_id<?php echo $area->reddesign_area_id?>" value="<?php echo $colorData[$j] ?>">
								</div>
							</td>
							</tr>
						<?php
						}
					}
				}
				?>
			</table>
			<input type="hidden" name="reddesign_color_code<?php echo $area->reddesign_area_id?>" id="reddesign_color_code<?php echo $area->reddesign_area_id?>" value="<?php echo $area->color_code?>">
			</div>
			</div>
		</div>
		</div>
<?php
}
