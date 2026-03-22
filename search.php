<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Minimum search length
if (strlen($query) < 1) {
    echo json_encode(['success' => true, 'results' => [], 'count' => 0]);
    exit;
}

try {
    // Advanced fuzzy search with relevance scoring (like YouTube/Google)
    $search_term = '%' . $query . '%';
    $exact_term = $query;
    
    // Split query into words for better matching
    $words = explode(' ', $query);
    $word_conditions = [];
    $word_params = [];
    
    foreach ($words as $word) {
        if (strlen(trim($word)) > 0) {
            $word_conditions[] = "(title LIKE ? OR description LIKE ? OR author LIKE ? OR genre LIKE ?)";
            $word_trim = '%' . trim($word) . '%';
            $word_params[] = $word_trim;
            $word_params[] = $word_trim;
            $word_params[] = $word_trim;
            $word_params[] = $word_trim;
        }
    }
    
    $word_condition_sql = !empty($word_conditions) ? 'OR (' . implode(' AND ', $word_conditions) . ')' : '';
    
    // Advanced SQL with relevance scoring
    $sql = "SELECT 
                id,
                title,
                description,
                cover,
                genre,
                author,
                chapters,
                rating,
                views,
                status,
                (
                    (CASE WHEN LOWER(title) = LOWER(?) THEN 1000 ELSE 0 END) +
                    (CASE WHEN LOWER(title) LIKE LOWER(?) THEN 500 ELSE 0 END) +
                    (CASE WHEN LOWER(title) LIKE LOWER(?) THEN 200 ELSE 0 END) +
                    (CASE WHEN LOWER(author) = LOWER(?) THEN 300 ELSE 0 END) +
                    (CASE WHEN LOWER(author) LIKE LOWER(?) THEN 100 ELSE 0 END) +
                    (CASE WHEN LOWER(genre) = LOWER(?) THEN 200 ELSE 0 END) +
                    (CASE WHEN LOWER(genre) LIKE LOWER(?) THEN 75 ELSE 0 END) +
                    (CASE WHEN LOWER(description) LIKE LOWER(?) THEN 50 ELSE 0 END) +
                    (rating * 10) +
                    (LEAST(views / 100, 25))
                ) as relevance_score
            FROM stories 
            WHERE (
                title LIKE ? 
                OR description LIKE ? 
                OR author LIKE ?
                OR genre LIKE ?
                $word_condition_sql
            )
            HAVING relevance_score > 0
            ORDER BY relevance_score DESC, rating DESC, views DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Prepare parameters
    $exact_lower = strtolower($exact_term);
    $starts_with = strtolower($exact_term) . '%';
    $contains = '%' . strtolower($exact_term) . '%';
    
    $params = [
        $exact_lower, $starts_with, $contains,
        $exact_lower, $contains,
        $exact_lower, $contains, $contains,
        $search_term, $search_term, $search_term, $search_term
    ];
    
    $params = array_merge($params, $word_params);
    $types = str_repeat('s', count($params));
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stories = [];
    while ($row = $result->fetch_assoc()) {
        $stories[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'] ? substr($row['description'], 0, 150) . '...' : 'No description',
            'cover' => $row['cover'],
            'genre' => $row['genre'],
            'author' => $row['author'],
            'chapters' => $row['chapters'],
            'rating' => $row['rating'],
            'views' => $row['views'],
            'status' => $row['status']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'count' => count($stories),
        'results' => $stories
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Search failed',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
