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
    $computersModeSum = Statistic::getModeSums($fields);
} else {
    $computersModeSum = Statistic::getModes();
    $computersModeSum = array_map(function ($arr) {
        $arr['computer_name'] = 'All computers';
        return $arr;
    }, $computersModeSum);
}

//var_dump($_REQUEST);
//var_dump($fields);
//var_dump($computersModeSum);

$modesAsJson = json_encode($computersModeSum);


$modes = [];

?>
<div class="page_title">Modes Report</div>

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
<?php foreach ($modes as $r) { ?>
    <option value="<?= $r['action']; ?>"><?= $r['action']; ?></option>
<?php } ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var modesJson = JSON.parse('<?= $modesAsJson; ?>');

    window.modesJson = modesJson;

    console.log(modesJson);

    const ctx = document.getElementById('myChart');

    const labels = modesJson.map(x => x.color_mode);

    const counts = modesJson.map(x => x.count);



    const data = [];
    for (i = 0; i < labels.length; i++) {
        data.push({
            color_mode: labels[i],
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

    const opacity = "0.7";

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(row => row.color_mode +
                ' ' + row.count + " (" + ((row.count / getArraySum(counts)) * 100).toFixed(0) + "%)"),
            datasets: [{
                label: "Mode DataSet",
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