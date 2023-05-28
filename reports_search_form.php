<?php

use BookScanner\Statistic;
use MyLibrary\helpers\HTMLHelper as html;

// $nameValue = $_POST['myVariable'];

// global $nameValue;


$nameValue = $GLOBALS['nameValue'];

?>
<form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>">
    <label for="computer_name">Computers:</label>
    <input type="hidden" name="view" value="<?php echo $nameValue; ?>" />
    <input type="hidden" name="sort_by" value="<?= !empty($sort_by) ? $sort_by : '' ?>" />

    <select name="computer_name[]" multiple class="chosen">
        <option></option>
        <?php foreach (Statistic::getComputers() as $r) { ?>
            <option value="<?= $r['computer_name'] ?>" <?= html::selectIf(is_array($fields['computer_name']) && in_array($r['computer_name'], $fields['computer_name'])) ?>><?= $r['computer_name'] ?>
            </option>
        <?php } ?>
    </select>


    <h2></h2>
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?= $fields['start_date'] ?>">

    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?= $fields['end_date'] ?>">

    <input type="submit" value="Submit">
</form>