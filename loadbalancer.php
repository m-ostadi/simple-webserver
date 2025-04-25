<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$workers = ['127.0.0.1:8001', '127.0.0.1:8002', '127.0.0.1:8003'];
$nextWorker = 0;

$server = stream_socket_server("tcp://0.0.0.0:8080", $errno, $errstr);
if (!$server) {
    die("Error: $errstr ($errno)\n");
}
echo "PHP Load Balancer running on http://localhost:8080\n";

//stream_set_timeout($server, 1); // Set timeout to 1 sec

while ($conn = @stream_socket_accept($server, -1)) {
    // Read full HTTP request
    $request = '';
    while (($line = fgets($conn)) !== false) {
        $request .= $line;
        if (rtrim($line) === '') break; // \r\n = End of headers
    }

    // Read body if present (Content-Length)
    if (preg_match('/Content-Length:\s*(\d+)/i', $request, $matches)) {
        $length = (int) $matches[1];
        $body = fread($conn, $length);
        $request .= $body;
    }

    // Pick a worker (round-robin)
    $target = $workers[$nextWorker];
    $nextWorker = ($nextWorker + 1) % count($workers);

    // Connect to the worker
    $worker = stream_socket_client("tcp://$target");
    if (!$worker) {
        fwrite($conn, "HTTP/1.1 502 Bad Gateway\r\n\r\nWorker unavailable.");
        fclose($conn);
        continue;
    }

    fwrite($worker, $request);

    // Relay response from worker to client
    while (!feof($worker)) {
        fwrite($conn, fread($worker, 8192));
    }

    fclose($worker);
    fclose($conn);
    gc_collect_cycles();
}
