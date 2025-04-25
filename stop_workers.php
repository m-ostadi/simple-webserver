<?php
$pidsFile = 'worker_pids.json';

if (!file_exists($pidsFile)) {
    exit("No PID file found.\n");
}

$pids = json_decode(file_get_contents($pidsFile), true);

foreach ($pids as $pid) {
    echo "Killing worker with PID $pid...\n";
    exec("kill $pid");
}

unlink($pidsFile);
echo "All workers stopped.\n";
