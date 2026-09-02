<?php
require_once 'db.php';

$YOUTUBE_API_KEY = 'AIzaSyBBmWv9tEqX6AiaJQuYH20-O0hz8VRXW9c'; // store in env var ideally, not hardcoded

function fetchVideosForQuery($query, $apiKey, $maxResults = 6) {
    $url = "https://www.googleapis.com/youtube/v3/search?" . http_build_query([
        'part'       => 'snippet',
        'q'          => $query,
        'type'       => 'video',
        'maxResults' => $maxResults,
        'safeSearch' => 'strict',
        'relevanceLanguage' => 'en',
        'key'        => $apiKey,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;
    return json_decode($response, true);
}

// Example: classes and a subject/topic list you want to top up
$classSubjectMap = [
    'class9'  => ['Science: Motion', 'Mathematics: Number Systems'],
    'class10' => ['Science: Chemical Reactions', 'Mathematics: Trigonometry'],
    'class11' => ['Physics: Units and Measurements'],
];

foreach ($classSubjectMap as $userClass => $topics) {
    foreach ($topics as $topicQuery) {
        $searchQuery = "$topicQuery $userClass CBSE explanation";
        $data = fetchVideosForQuery($searchQuery, $YOUTUBE_API_KEY);

        if (!$data || empty($data['items'])) continue;

        foreach ($data['items'] as $item) {
            $videoId  = $item['id']['videoId'] ?? null;
            if (!$videoId) continue;

            $title     = $conn->real_escape_string($item['snippet']['title']);
            $thumb     = $conn->real_escape_string($item['snippet']['thumbnails']['medium']['url']);
            $channel   = $conn->real_escape_string($item['snippet']['channelTitle']);
            $subject   = $conn->real_escape_string(explode(':', $topicQuery)[0]);
            $topicName = $conn->real_escape_string(trim(explode(':', $topicQuery)[1] ?? ''));

            $stmt = $conn->prepare(
                "INSERT IGNORE INTO class_videos
                 (user_class, subject, topic, youtube_video_id, title, thumbnail_url, channel_title)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('sssssss', $userClass, $subject, $topicName, $videoId, $title, $thumb, $channel);
            $stmt->execute();
            $stmt->close();
        }
    }
}

echo "Done fetching videos.\n";
$conn->close();