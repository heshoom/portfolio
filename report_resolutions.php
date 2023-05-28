<?php
require_once 'includes/view_included.php';

use \BookScanner\Statistic;
use \MyLibrary\helpers\HTMLHelper as html;
use MyLibrary\helpers\StringHelper as str;

$fields = [];
$fields['computer_name'] = array_key_exists('computer_name', $_REQUEST) ? (array)$_REQUEST['computer_name'] : '';

$fields['start_date'] = array_key_exists('start_date', $_REQUEST) ? $_REQUEST['start_date'] : '';

$fields['end_date'] = array_key_exists('end_date', $_REQUEST) ? $_REQUEST['end_date'] : '';




if ($fields['computer_name'] != '' || $fields['start_date'] != '') {
    $computersResolutionSum = Statistic::getResolutionSums($fields);
} else {
    $computersResolutionSum = Statistic::getResolutions();
    // var_dump(gettype($computersResolutionSum));
    $computersResolutionSum = array_map(function ($arr) {
        $arr['computer_name'] = 'All computers';
        return $arr;
    }, $computersResolutionSum);
}

// var_dump($_REQUEST);
// var_dump($fields);

$ResolutionsAsJson = json_encode($computersResolutionSum);

$Resolutions = [];
?>
<div class="page_title">Resolutions Report</div>

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
<?php foreach ($Resolutions as $r) { ?>
    <option value="<?= $r['resolution']; ?>"><?= $r['Resolution']; ?></option>
<?php } ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ResolutionsJson = JSON.parse('<?= $ResolutionsAsJson; ?>');

    window.ResolutionsJson = ResolutionsJson;

    console.log(ResolutionsJson);

    const ctx = document.getElementById('myChart');

    const labels = ResolutionsJson.map(x => x.resolution);

    const counts = ResolutionsJson.map(x => x.count);

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

    //var sumCounts = (row.count / getArraySum(counts)) * 100);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(row => row.year +
                ' ' + row.count + " (" + ((row.count / getArraySum(counts)) * 100).toFixed(0) + "%)"),
            datasets: [{
                label: 'Resolution Data Set',
                data: data.map(row => row.count),
                backgroundColor: [
                    //'rgba(255, 99, 132, 0.2)',
                    //'rgba(255, 159, 64, 0.2)',
                    //'rgba(255, 205, 86, 0.2)',
                    `rgba(75, 192, 192, ${opacity})`,
                    `rgba(54, 162, 235, ${opacity})`,
                    `rgba(153, 102, 255, ${opacity})`,
                    `rgba(201, 203, 207, ${opacity})`
                ],
            }]
        }
    })
</script>