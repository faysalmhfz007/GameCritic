<?php
require_once 'app/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Check if games table exists and is empty
    $result = $conn->query('SELECT COUNT(*) as count FROM games');
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "Database is empty. Adding sample games...<br>";
        
        // Insert sample games
        $games = [
            [
                'title' => 'The Legend of Zelda: Breath of the Wild',
                'genre' => 'Adventure',
                'platform' => 'Nintendo Switch',
                'release_year' => 2017,
                'cover_image' => '/images/zelda.jpg',
                'description' => 'An open-world adventure game featuring Link in a vast, beautiful world.',
                'review' => 'A masterpiece of open-world design with innovative gameplay mechanics.',
                'pos_count' => 1250,
                'neg_count' => 45,
                'overall_score' => 9.5
            ],
            [
                'title' => 'God of War',
                'genre' => 'Action-Adventure',
                'platform' => 'PlayStation 4',
                'release_year' => 2018,
                'cover_image' => '/images/godofwar.jpg',
                'description' => 'Kratos returns in this epic Norse mythology adventure.',
                'review' => 'Stunning visuals and emotional storytelling make this a must-play action game.',
                'pos_count' => 2100,
                'neg_count' => 78,
                'overall_score' => 9.8
            ],
            [
                'title' => 'Elden Ring',
                'genre' => 'Action RPG',
                'platform' => 'Multi-platform',
                'release_year' => 2022,
                'cover_image' => '/images/eldenring.jpg',
                'description' => 'FromSoftware\'s latest challenging action RPG in an open world.',
                'review' => 'A challenging yet rewarding experience that redefines the Souls-like genre.',
                'pos_count' => 3200,
                'neg_count' => 156,
                'overall_score' => 9.7
            ],
            [
                'title' => 'Cyberpunk 2077',
                'genre' => 'RPG',
                'platform' => 'Multi-platform',
                'release_year' => 2020,
                'cover_image' => '/images/cyberpunk.jpg',
                'description' => 'An open-world action-adventure story set in the megalopolis of Night City.',
                'review' => 'Despite its rocky launch, Cyberpunk 2077 offers an immersive futuristic experience.',
                'pos_count' => 1800,
                'neg_count' => 420,
                'overall_score' => 7.8
            ],
            [
                'title' => 'The Witcher 3: Wild Hunt',
                'genre' => 'RPG',
                'platform' => 'Multi-platform',
                'release_year' => 2015,
                'cover_image' => '/images/witcher3.jpg',
                'description' => 'Geralt of Rivia\'s final adventure in this epic fantasy RPG.',
                'review' => 'One of the greatest RPGs ever made with incredible storytelling and world-building.',
                'pos_count' => 4500,
                'neg_count' => 89,
                'overall_score' => 9.9
            ]
        ];
        
        $stmt = $conn->prepare("INSERT INTO games (title, genre, platform, release_year, cover_image, description, review, pos_count, neg_count, overall_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($games as $game) {
            $stmt->bind_param("sssisssiid", 
                $game['title'], 
                $game['genre'], 
                $game['platform'], 
                $game['release_year'], 
                $game['cover_image'], 
                $game['description'], 
                $game['review'], 
                $game['pos_count'], 
                $game['neg_count'], 
                $game['overall_score']
            );
            $stmt->execute();
            echo "Added: " . $game['title'] . "<br>";
        }
        
        echo "<br>Database populated successfully!<br>";
        echo "<a href='public/'>Go to Home Page</a>";
        
    } else {
        echo "Database already has " . $row['count'] . " games.<br>";
        echo "<a href='public/'>Go to Home Page</a>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
