# Jalankan Laravel dengan PHP 8.4 + extension MongoDB
$php84 = "C:\Users\rayir\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

if (-not (Test-Path $php84)) {
    Write-Error "PHP 8.4 tidak ditemukan. Install dengan: winget install PHP.PHP.8.4"
    exit 1
}

$hasMongo = & $php84 -m 2>$null | Select-String -Pattern '^mongodb$'
if (-not $hasMongo) {
    Write-Error "Extension MongoDB belum aktif di PHP 8.4. Restart terminal lalu coba lagi."
    exit 1
}

Write-Host "Using: $php84" -ForegroundColor Green
& $php84 artisan serve @args
