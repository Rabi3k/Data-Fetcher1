<?php
const VERSION = '0.0.100';
if (!defined('SleekDB_DIR')) {
    define('SleekDB_DIR', __DIR__ . '/textsDb');
}


$textsStore = new \SleekDB\Store("texts", SleekDB_DIR, [
    'auto_cache' => true,
    "cache_lifetime" => 10,
    'timeout' => false
]);
