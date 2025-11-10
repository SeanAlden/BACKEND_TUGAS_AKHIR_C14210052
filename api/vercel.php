<?php

// Override default CORS injected by vercel-php
header("Access-Control-Allow-Origin: https://asiaraya.netlify.app");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Continue to Laravel
require __DIR__ . '/../public/index.php';
