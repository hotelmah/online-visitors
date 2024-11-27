<?php

namespace KAPNET;

require_once('Includes/function.onlinevisitors.php');

//* ===================================================================================================================== */

executeOnlineVisitorsInsertRow();

$SelectOptionsAry = executeGetOnlineVisitorsLatestIPAddressesWithTime();

//* ===================================================================================================================== */

$NewLine = chr(0x0D);
$SelectOptions = "";

foreach ($SelectOptionsAry as $Key => $Value) {
    $SelectOptions .= "\t\t\t\t" . '<option value="' . $Key + 1 . '">' . $Value . '</option>' . PHP_EOL;
}

// Remove First 3x Tab Characters
$SelectOptions = substr($SelectOptions, 4);

// Remove Last PHP_EOL
$SelectOptions = substr($SelectOptions, 0, -1);

/* ===================================================================================================================== */

$HTML = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online Visitors">
    <title>Online Visitors Test</title>
</head>
<body>
    <main>
        <form name ="FrmSelOptions" action="" method="get">
            <label for="SelOnlineVisitors">Online Visitors: </label>
            <select name="SelOnlineVisitors" id="SelOnlineVisitors">
                <option value="0" selected></option>{$NewLine}
                {$SelectOptions}
            </select>
        </form>
    </main>
</body>
</html>
HTML;

/* ===================================================================================================================== */

echo $HTML;

/* ===================================================================================================================== */
