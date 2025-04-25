<?php
$ports = [8001, 8002, 8003];
$pids = [];

foreach ($ports as $port) {
    $cmd = "php -S 127.0.0.1:$port -t public > /dev/null 2>&1 & echo $!";
    $pid = trim(shell_exec($cmd));
    $pids[] = $pid;
    echo "Started worker on port $port (PID $pid)\n";
}

// Save PIDs to file
file_put_contents('worker_pids.json', json_encode($pids));
