<?php declare(strict_types=1);
session_cache_limiter('nocache');
header('Content-Type: text/html; charset=UTF-8');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: DENY');
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: -1");
session_start();
require_once(__DIR__. '/../DB/LoginWay.php');
require_once(__DIR__. '/../DB/UserModel.php');

// Debugging: Check if image data is present in session
// if (!isset($_SESSION['imageData'])) {
//     echo 'No image data found in session';
//     exit();
// }
var_dump($_SESSION['image']['type']);
// Content-Type based on image type
// switch ($_SESSION['image']['type'])
// {
//     case 'image/jpeg':
//     case 'image/jpg':  // Combine both cases
//         header('Content-type: image/jpeg');
//         break;
//     case 'image/png':
//         header('Content-type: image/png');
//         break;
//     case 'image/gif':
//         header('Content-type: image/gif');
//         break;
//     default:
//         header('Content-type: image/png');
//         echo 'Unsupported image type';
//         exit();
// }

// // Output image data (check if it's base64 encoded)
// if (base64_decode($_SESSION['imageData'], true) !== false) {
//     // If it's base64 encoded
//     echo base64_decode($_SESSION['imageData']);
// } else {
//     // If it's raw binary data
//     echo $_SESSION['imageData'];
// }