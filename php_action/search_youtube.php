<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Not logged in']); exit; }

header('Content-Type: application/json');

$YOUTUBE_API_KEY = ''; // same key as fetch_youtube_videos.php

$query = trim($_GET['q'] ?? '');
if ($query === '') { echo json_encode(['error' => 'Empty query']); exit; }

$url = "https://www.googleapis.com/youtube/v3/search?" . http_build_query([
    'part'              => 'snippet',
    'q'                 => $query,
    'type'              => 'video',
    'maxResults'        => 12,
    'safeSearch'        => 'strict',
    'relevanceLanguage' => 'en',
    'key'               => $YOUTUBE_API_KEY,
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['error' => 'YouTube API request failed', 'http_code' => $httpCode, 'curl_error' => $curlError]);
    exit;
}

$data = json_decode($response, true);
$videos = [];
foreach ($data['items'] ?? [] as $item) {
    $videoId = $item['id']['videoId'] ?? null;
    if (!$videoId) continue;
    $videos[] = [
        'video_id'      => $videoId,
        'title'         => $item['snippet']['title'],
        'thumbnail'     => $item['snippet']['thumbnails']['medium']['url'],
        'channel_title' => $item['snippet']['channelTitle'],
    ];
}

echo json_encode(['videos' => $videos]);
