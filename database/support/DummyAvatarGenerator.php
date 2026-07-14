<?php

namespace Database\Support;

/**
 * Seed-only utility: generates a placeholder avatar (initials over a colored
 * background) so seeded demo accounts have something to show in the UI.
 * Never used at runtime — only from DatabaseSeeder/factories.
 */
class DummyAvatarGenerator
{
    private const COLORS = [
        '#4f46e5', '#db2777', '#0d9488', '#d97706',
        '#7c3aed', '#dc2626', '#0284c7', '#16a34a',
    ];

    public static function generate(string $name): string
    {
        $size = 256;
        $image = imagecreatetruecolor($size, $size);

        [$r, $g, $b] = sscanf(self::colorFor($name), '#%02x%02x%02x');
        imagefill($image, 0, 0, imagecolorallocate($image, $r, $g, $b));

        $white = imagecolorallocate($image, 255, 255, 255);
        $fontPath = resource_path('fonts/DejaVuSans-Bold.ttf');
        $fontSize = 90;
        $initials = self::initials($name);

        $box = imagettfbbox($fontSize, 0, $fontPath, $initials);
        $textWidth = abs($box[4] - $box[0]);
        $textHeight = abs($box[5] - $box[1]);

        imagettftext(
            $image,
            $fontSize,
            0,
            (int) (($size - $textWidth) / 2),
            (int) (($size + $textHeight) / 2),
            $white,
            $fontPath,
            $initials
        );

        ob_start();
        imagepng($image);

        return ob_get_clean();
    }

    private static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $initials = mb_strtoupper(mb_substr($parts[0], 0, 1));

        if (count($parts) > 1) {
            $initials .= mb_strtoupper(mb_substr(end($parts), 0, 1));
        }

        return $initials;
    }

    private static function colorFor(string $name): string
    {
        return self::COLORS[crc32($name) % count(self::COLORS)];
    }
}
