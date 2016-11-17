<?php
$client= new GearmanClient();
$client->addServer('gearman');
$testCases = explode("\n", shell_exec("./getTestList.sh"));
print_r($testCases);
foreach ($testCases as $testName) {
    if (empty($testName)) {
        continue;
    }
    print $client->do("runTest", json_encode(array('testName' => $testName)));
}
?>
