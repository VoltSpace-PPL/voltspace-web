Add-Type -AssemblyName System.Drawing
$srcPath = "$PSScriptRoot\public\images\voltspace-logo.png"
$destPath = "$PSScriptRoot\public\images\favicon.png"

if (!(Test-Path $srcPath)) {
    Write-Host "File gambar tidak ditemukan di $srcPath"
    exit
}

# Load the original image
$img = [System.Drawing.Image]::FromFile($srcPath)
$size = [math]::Min($img.Width, $img.Height)

# Create a new transparent bitmap
$bmp = New-Object System.Drawing.Bitmap $size, $size
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$g.Clear([System.Drawing.Color]::Transparent)

# Create a circular clipping path
$path = New-Object System.Drawing.Drawing2D.GraphicsPath
$path.AddEllipse(0, 0, $size, $size)
$g.SetClip($path)

# Draw the image into the circle
$x = ($size - $img.Width) / 2
$y = ($size - $img.Height) / 2
$g.DrawImage($img, $x, $y, $img.Width, $img.Height)

# Save as new PNG
$bmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)

# Cleanup
$g.Dispose()
$img.Dispose()
$bmp.Dispose()

Write-Host "Berhasil! Gambar telah dipotong menjadi lingkaran sempurna dan disimpan sebagai favicon.png"
