<?php
// Enable CORS for your subdomain
header('Access-Control-Allow-Origin: https://divorceeasy.thefuturesmachines.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['prompt']) || empty($input['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Prompt is required']);
    exit();
}

$prompt = $input['prompt'];

// ⚠️ IMPORTANT: Add your Gemini API key here (keep this file secure!)
$apiKey = 'YOUR_GEMINI_API_KEY_HERE';

// Gemini API endpoint
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;

// Prepare the request payload
$payload = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ]
];

// Make the API request
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Return the response
http_response_code($httpCode);
echo $response;
?>
