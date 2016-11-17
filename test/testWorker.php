<?php
echo 'Starting gearman worker';
$worker= new GearmanWorker();
echo 'Adding server';
$worker->addServer('gearman');
echo 'Capture function';
$worker->addFunction("runTest", "runTest");
while ($worker->work());

function runTest($job)
{
    $testData = json_decode($job->workload(), true);
    passthru("cd /var/www/public/docker && docker-compose -f docker-compose-isolated-test.yml up -d");
    passthru("cd /var/www/public/ && dockerize -wait tcp://opencats_test_mariadb:3306 -wait http://opencats_test_web:80 -timeout 60s && php modules/tests/waitForDb.php && cat test/behat.yml && pwd");
    passthru("cd /var/www/public/docker && docker-compose -f docker-compose-isolated-test.yml exec php /var/www/public/test/runTest.sh " . escapeshellarg($testData['testName']));
    passthru("cd /var/www/public/docker && docker-compose -f docker-compose-isolated-test.yml down");
}
?>