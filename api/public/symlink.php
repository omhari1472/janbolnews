<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (file_exists($link)) {
    echo "The link already exists.";
} else {
    symlink($target, $link);
    echo "The link has been created.";
}
