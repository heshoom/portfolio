<?php
require_once 'includes/view_included.php';

use BookScanner\Statistic;
use MyLibrary\helpers\HTMLHelper as html;
use MyLibrary\helpers\StringHelper as str;

$fields = [];
$fields['computer_name'] = array_key_exists('computer_name', $_REQUEST) ? (array) $_REQUEST['computer_name'] : '';

$fields['start_date'] = array_key_exists('start_date', $_REQUEST) ? $_REQUEST['start_date'] : '';

$fields['end_date'] = array_key_exists('end_date', $_REQUEST) ? $_REQUEST['end_date'] : '';

if ($fields['computer_name'] != '' || $fields['start_date'] != '') {
	$computersFormatSums = Statistic::getFormatSums($fields);
} else {
	$computersFormatSums = Statistic::getFormats();
	// var_dump(gettype($computersFormatSums));
	$computersFormatSums = array_map(function ($arr) {
		$arr['computer_name'] = 'All computers';
		return $arr;
	}, $computersFormatSums);
}

// var_dump($_REQUEST);
// var_dump($fields);

$formatsAsJson = json_encode($computersFormatSums);

$formats = [];
?>
<div class="page_title">Formats Report</div>

<?php
$filename = basename(__FILE__);
?>

<form id="myForm" action="<?php echo $filename ?>" $method="POST">
	<input type="hidden" name="myVariable" value="<?php echo htmlspecialchars($filename); ?>">
</form>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		var form = document.getElementById('myForm');
		// form.submit();
	});
</script>


<?php

$GLOBALS['nameValue'] = str::getSubStringBefore(basename(__FILE__), '.php');

require_once(__DIR__ . '/reports_search_form.php');
?>




<div>
	<canvas id="myChart"></canvas>
</div>
<?php foreach ($formats as $r) { ?>
	<span value="<?= $r['format'] ?>"><?= $r['format'] ?></span>
<?php } ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	var formatsAsJson = JSON.parse('<?= $formatsAsJson ?>');

	window.formatsAsJson = formatsAsJson;

	console.log(formatsAsJson);

	const ctx = document.getElementById('myChart');

	const labels = formatsAsJson.map(x => x.format);

	const counts = formatsAsJson.map(x => x.count);

	const opacity = "0.7";

	const data = [];
	for (i = 0; i < labels.length; i++) {
		data.push({
			year: labels[i],
			count: counts[i],
		})
	}

	function getArraySum(arr) {
		var sum = 0;
		for (var i = 0; i < arr.length; i++) {
			sum += parseInt(arr[i]);
		}
		return sum;
	}


	new Chart(ctx, {
		type: 'bar',
		data: {
			labels: data.map(row => row.year +
				' ' + row.count + " (" + ((row.count / getArraySum(counts)) * 100).toFixed(0) + "%)"),
			datasets: [{
				label: 'Format DataSet',
				data: data.map(row => row.count),
				backgroundColor: [
					`rgba(255, 99, 132, ${opacity})`,
					`rgba(255, 159, 64, ${opacity})`,
					`rgba(255, 205, 86, ${opacity})`,
					`rgba(75, 192, 192, ${opacity})`,
					`rgba(54, 162, 235, ${opacity})`,
					`rgba(153, 102, 255, ${opacity})`,
					`rgba(201, 203, 207, ${opacity})`
				],
			}]
		}
	})
</script>