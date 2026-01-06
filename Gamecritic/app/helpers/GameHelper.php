<?php
class GameHelper {
    // Normalize cover image path
    public static function normalizeCoverImage($coverImage, $baseUrl) {
        if (empty($coverImage)) {
            return $baseUrl . '/images/default.jpg';
        }

        // If already a full URL, return as is
        if (strpos($coverImage, 'http://') === 0 || strpos($coverImage, 'https://') === 0) {
            return $coverImage;
        }

        // If starts with /images/, prepend baseUrl
        if (strpos($coverImage, '/images/') === 0) {
            return $baseUrl . $coverImage;
        }

        // If starts with images/ (no leading slash), add leading slash and baseUrl
        if (strpos($coverImage, 'images/') === 0) {
            return $baseUrl . '/' . $coverImage;
        }

        // Otherwise, assume it's a filename and add /images/ prefix
        return $baseUrl . '/images/' . $coverImage;
    }

    // Normalize cover images in array of games
    public static function normalizeGamesCoverImages($games, $baseUrl) {
        foreach ($games as &$game) {
            $game['cover_image_normalized'] = self::normalizeCoverImage($game['cover_image'] ?? '', $baseUrl);
        }
        return $games;
    }
}
?>
