<?php
require_once("includes/view_included.php");

use \MyLibrary\PagedResult;
use \MyLibrary\PDO_Wrapper;
use \MyLibrary\AttributeReader;
use \MyLibrary\helpers\StringHelper as str;
use \MyLibrary\helpers\GeneralHelper as gen;
use \MyLibrary\helpers\HTMLHelper as html;
use \MyLibrary\helpers\TimeHelper as time;

use \BookScanner\Statistic;

$status->setDestination("Statistics");

// prepare and retrieve paged results
$pager = new PagedResult();
$pager->results_per_page = gen::format($_REQUEST, 'items_per_page', ['?int!'], 20);
$pager->current_page 	 = gen::format($_REQUEST, 'page', ['?int!'], 1);
$pager->OrderBy($sort_by = gen::format($_REQUEST, 'sort_by', ['?trim', '?sort_by!'], 'session_id_a'));

$fields = array();
$fields['session_id'] = gen::format($_REQUEST, 'session_id', ['?str!', '?trim'], '');
$fields['timestamp'] = gen::format($_REQUEST, 'timestamp', ['?date!'], '');
$fields['action'] = gen::format($_REQUEST, 'action', ['?str!', '?trim'], '');
$fields['count'] = gen::format($_REQUEST, 'count', ['?trim', '?int!'], '');
$fields['computer_name'] = gen::format($_REQUEST, 'computer_name', ['?str!', '?trim'], '');
$fields['color_mode'] = gen::format($_REQUEST, 'color_mode', ['?str!', '?trim'], '');
$fields['resolution'] = gen::format($_REQUEST, 'resolution', ['?str!', '?trim'], '');
$fields['format'] = gen::format($_REQUEST, 'format', ['?str!', '?trim'], '');

$pager->conditions[] = array(
	'AND' => array(
		'session_id' => array($fields['session_id'], 'like'),
		'timestamp' => array($fields['timestamp'], 'date_eq'),
		'action' => array($fields['action'], 'like'),
		'count' => array($fields['count'], 'eq'),
		'computer_name' => array($fields['computer_name'], 'like'),
		'color_mode' => array($fields['color_mode'], 'like'),
		'resolution' => array($fields['resolution'], 'like'),
		'format' => array($fields['format'], 'like'),
	)
);
$pager->table = Statistic::tableName();

$pager->RunSearch();

if (PDO_Wrapper::isError($pager->results)) {
	return handle_error($status, $pager->results);
}

//var_dump(Statistic::getComputers());
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#batch_delete').click(function() {
			if ($('input.Statistics:checked').size() == 0) {
				alert('Please select at least one item to delete');
				return false;
			}

			if (confirm("Are you sure that you want to remove the selected items?")) {
				var link = 'controller.php?action=delete_Statistic';
				$('input.Statistics:checked').each(function() {
					link += '&id[]=' + $(this).val();
				});
				link += '&popup_destination=<?= @$popup_destination; ?>';
				location.href = (link);
			}
		});
		$('#Statistics_all').click(function() {
			$('.Statistics').attr('checked', $('#Statistics_all').attr('checked'));
		});
		$('.Statistics').click(function() {
			if ($(this).attr('checked') == false && $('#Statistics_all').attr('checked'))
				$('#Statistics_all').removeAttr('checked');
		});
	});
</script>

<div class="page_title">Manage Statistics</div>

<form method="get" action="<?= $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="view" value="Statistics" />
	<input type="hidden" name="sort_by" value="<?= (!empty($sort_by) ? $sort_by : ''); ?>" />

	<div id="search_options" style="display1:<?= (array_empty($fields) ? "none" : "block") ?>">
		<div class="text_tabs">
			<ul>
				<li class="left spacer">&nbsp;</li>
				<li><b><span>Search Options</span></b></li>
				<li class="right"><a href="javascript:void(0)" onclick="$('#search_options').fadeOut('fast');">Hide Search Options</a></li>
			</ul>
		</div>
		<div class="tabbed_section_body">

			<div class="search_field_wrapper" id="sf_session_id">
				<div class="search_field_label">Session:</div>
				<div class="search_field">
					<input type='text' class='textbox form-control' name='session_id' value='<?= $fields['session_id']; ?>' maxlength='255' />
				</div>
			</div>

			<div class="search_field_wrapper" id="sf_timestamp">
				<div class="search_field_label">Date Range:</div>
				<div class="search_field">
					<script type='text/javascript'>
						DateTimeInput('timestamp', false, 'date', '<?php if ($fields['timestamp'] != '') {
																		echo date('Y-m-d G:i:s', strtotime($fields['timestamp']));
																	} ?>')
					</script>
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="search_field_wrapper" id="sf_action">
				<div class="search_field_label">Action:</div>
				<div class="search_field">
					<input type='text' class='textbox form-control' name='action' value='<?= $fields['action']; ?>' maxlength='255' />
				</div>
			</div>

			<div class="search_field_wrapper" id="sf_count">
				<div class="search_field_label">Count:</div>
				<div class="search_field">
					<input type='text' class='textbox form-control' name='count' value='<?= $fields['count']; ?>' />
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="search_field_wrapper" id="sf_computer_name">
				<div class="search_field_label">Computer Name:</div>
				<div class="search_field">
					<select name="computer_name" multiple class="chosen">
						<option></option>
						<?php foreach (Statistic::getComputers() as $r) { ?>
							<option value="<?= $r['computer_name']; ?>" <?= html::selectIf($r['computer_name'] == $fields['computer_name']); ?>><?= $r['computer_name']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="search_field_wrapper" id="sf_color_mode">
				<div class="search_field_label">Color Mode:</div>
				<div class="search_field">
					<input type='text' class='textbox form-control' name='color_mode' value='<?= $fields['color_mode']; ?>' maxlength='255' />
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="search_field_wrapper" id="sf_resolution">
				<div class="search_field_label">Resolution:</div>
				<div class="search_field">
					<input type='text' class='textbox form-control' name='resolution' value='<?= $fields['resolution']; ?>' maxlength='255' />
				</div>
			</div>

			<div class="search_field_wrapper" id="sf_format">
				<div class="search_field_label">Format:</div>
				<div class="search_field">
					<input type='text' class='textbox form-control' name='format' value='<?= $fields['format']; ?>' maxlength='255' />
				</div>
			</div>
			<div style="clear: both;"></div>
			<div style="clear: both;"></div>
			<div class="search_field_wrapper">
				<div class="search_field_label">Results:</div>
				<div class="search_field">
					<?php
					$results = $pager->total_results . " match" . ($pager->total_results != 1 ? "es" : "") . " found.";
					if ($pager->total_results > 0)
						$results .= " Page " . ($pager->current_page) . " of " . $pager->total_pages . ".";
					?>
					<div><?= $results; ?></div>
				</div>
			</div>
			<div class="search_field_wrapper">
				<div class="search_field_label"></div>
				<div class="search_field_label">
					<input type="submit" value="Search" class="button btn-search" style="width:80px;" />
					<a href="?view=Statistics&reset" class="button btn-reload">reset</a>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>

	<br />

	<div class="filter_box">
		<table width="100%">
			<tr>
				<?php if (accessible_permission("add_Statistic", "view")) { ?>
					<td style="padding-right:10px;width:131px;">
						<a href="?view=add_Statistic" class="button btn-add popup-window1">Add Statistic</a>
					</td>
				<?php } ?>
				<td align="center">
					<?php $pager->getPager(); ?>
				</td>
				<td align="right">
					<input type="button" value="Search Options" class="button btn-search" onclick="$('#search_options').fadeIn('fast');" style="width:130px;" />
				</td>
			</tr>
		</table>
	</div>

	<table class="grid-def">
		<tr class="grid-row_header">
			<?php if (accessible_permission("delete_Statistic_model", "model")) { ?>
				<td width="50" align="center">Delete</td>
				<!--<td width="40" align="center" style="padding:0"><input type="checkbox" id="Statistics_all" title="Select all items on this page"/></td>-->
			<?php } ?>
			<td>
				Session
				<input type="image" onclick="sort_by.value='session_id_a';" class="commonImages-arrow-up" />
				<input type="image" onclick="sort_by.value='session_id_d';" class="commonImages-arrow-down" />
			</td>
			<td>Action
				<input type="image" onclick="sort_by.value='session_id_a';" class="commonImages-arrow-up" />
				<input type="image" onclick="sort_by.value='session_id_d';" class="commonImages-arrow-down" />
			</td>
			<td>Mode
				<input type="image" onclick="sort_by.value='color_mode_a';" class="commonImages-arrow-up" />
				<input type="image" onclick="sort_by.value='color_mode_d';" class="commonImages-arrow-down" />
			</td>
			<td>Resolution
				<input type="image" onclick="sort_by.value='resolution_a';" class="commonImages-arrow-up" />
				<input type="image" onclick="sort_by.value='resolution_d';" class="commonImages-arrow-down" />
			</td>
			<td>Format
				<input type="image" onclick="sort_by.value='format_a';" class="commonImages-arrow-up" />
				<input type="image" onclick="sort_by.value='format_d';" class="commonImages-arrow-down" />
			</td>
		</tr>

		<?php
		$i = 0;
		foreach ($pager->results as $row) {
			$i++;
			$statistic = new Statistic();
			$statistic->setPropertyValues($row);
		?>
			<tr class="grid-row<?= ($i % 2) ?>">
				<?php if (accessible_permission("delete_Statistic_model", "model")) { ?>
					<td align="center">
						<a href='controller.php?action=delete_Statistic&id=<?= $statistic->id; ?>&popup_destination=<?= @$popup_destination; ?>' onClick='return confirm("Are you sure that you want to delete this Statistic?");'><img class="commonImages-delete" /></a>
						<!--<input type="checkbox" class="Statistics" name="id[]" value="<?= $statistic->id; ?>"/>-->
					</td>
				<?php } ?>
				<td>
					<?php
					$updateLink = "<a href='?view=add_Statistic&id=" . $statistic->id . "' class='popup-window1'>" . $statistic->session_id . "</a>";
					echo (accessible_permission("add_Statistic", "view") ? $updateLink : strip_tags($updateLink));
					?>
					<div><?= time::getInFormat($statistic->timestamp, time::$displayDayDateAndTimeFormat); ?></div>
				</td>
				<td>
					<?php echo $statistic->action; ?>
				</td>
				<td>
					<?php echo $statistic->color_mode; ?>
				</td>
				<td>
					<?php echo $statistic->resolution; ?>
				</td>
				<td>
					<?php echo $statistic->format; ?>
				</td>
			</tr>
		<?php
		}
		if ($pager->total_results == 0) {
		?>
			<tr class="grid-row0">
				<td colspan="7" align="center" height="50">No items found matching your query.</td>
			</tr>
		<?php
		}
		?>
	</table>

	<table class="grid-def" style="margin-top:10px;border:0">
		<tr>
			<td style="padding-left:10px;">
				<?php /*if (accessible_permission("delete_Statistic_model","model")) { ?>
				<b>Action:</b> <a href='javascript:void(0)' class="link btn-delete" id='batch_delete'>Remove selected</a>
				<?php }*/ ?>
			</td>
			<td colspan="2" align="right"><?= $results; ?></td>
		</tr>
	</table>

</form>