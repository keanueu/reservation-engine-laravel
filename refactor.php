<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views/home');
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $original = $content;

        // 1. Font Weights
        $content = preg_replace('/\bfont-(bold|semibold|extrabold|black|light|normal)\b/', 'font-medium', $content);

        // 2. Tracking (remove completely)
        $content = preg_replace('/\btracking-([a-zA-Z0-9\-\.\[\]]+)\b\s*/', '', $content);

        // 3. Leading
        $content = preg_replace('/\bleading-([a-zA-Z0-9\-\.\[\]]+)\b/', 'leading-relaxed', $content);

        // 4. Color Palette
        // Dark grays to black
        $content = preg_replace('/\btext-gray-[5-9]00\b/', 'text-black', $content);
        // Light grays to white
        $content = preg_replace('/\btext-gray-[1-4]00\b/', 'text-white', $content);
        $content = preg_replace('/\btext-gray-50\b/', 'text-white', $content);

        // Background grays? The user only mentioned fonts "do not use gray anymore because i dont like that kind of font". 
        // We will leave background grays (e.g. bg-gray-50) alone as they didn't specify backgrounds.

        // 5. Font Sizes
        $content = preg_replace('/\btext-\[10px\]\b/', 'text-sm', $content);
        $content = preg_replace('/\btext-xs\b/', 'text-sm', $content);

        if ($original !== $content) {
            file_put_contents($path, $content);
            echo "Updated: " . $path . "\n";
        }
    }
}

echo "Done refactoring.\n";
