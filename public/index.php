<?php

$routes = [
    ['GET', '#^/$#', function () {
        echo "<h1>Welcome to My PHP Server</h1>";
        echo "<p><a href='/hello/Ali'>Say Hello to Ali</a></p>";
    }],
    ['GET', '#^/hello/([a-zA-Z0-9_-]+)$#', function ($name) {
        echo "<h1>Hello, " . htmlspecialchars($name) . "!</h1>";
    }],
    ['GET', '#^/api/time$#', function () {
        header('Content-Type: application/json');
        echo json_encode(['time' => date('c')]);
    }],
];

// Serve static files if they exist
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && file_exists($file)) {
    return false; // Let the server serve the static file
}

echo "<p>Served by PID: " . getmypid() . "</p>";


// Match route
$method = $_SERVER['REQUEST_METHOD'];
$found = false;

foreach ($routes as [$routeMethod, $pattern, $handler]) {
    if ($method === $routeMethod && preg_match($pattern, $path, $matches)) {
        array_shift($matches); // Remove full match
        call_user_func_array($handler, $matches);
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
}
