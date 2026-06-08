<?php
$filePath = 'public/images/voltspace-logo.png';
if (!file_exists($filePath)) {
    die("File not found");
}

$src = @imagecreatefromstring(file_get_contents($filePath));
if (!$src) {
    die("Could not read image");
}

$w = imagesx($src);
$h = imagesy($src);
$min = min($w, $h);

$dest = imagecreatetruecolor($min, $min);
imagealphablending($dest, false);
$transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
imagefill($dest, 0, 0, $transparent);
imagesavealpha($dest, true);

// Create a circular mask
for ($x = 0; $x < $min; $x++) {
    for ($y = 0; $y < $min; $y++) {
        $dx = $x - $min/2;
        $dy = $y - $min/2;
        // Check if inside the circle
        if ($dx*$dx + $dy*$dy <= ($min/2)*($min/2)) {
            $sx = $x + ($w - $min)/2;
            $sy = $y + ($h - $min)/2;
            $color = imagecolorat($src, (int)$sx, (int)$sy);
            imagesetpixel($dest, $x, $y, $color);
        }
    }
}
imagepng($dest, $filePath);
echo "Image successfully made circular\n";
