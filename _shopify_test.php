<?php
/**
 * Temporary diagnostic: compare fetch_url() against several Shopify stores.
 * Delete after use.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$urls = array_slice($argv, 1);
if (!$urls) {
    $urls = [
        'https://www.allbirds.com/',
        'https://gymshark.com/',
        'https://www.fashionnova.com/',
    ];
}

foreach ($urls as $u) {
    $f = fetch_url($u, 15);
    printf("%-38s status=%-4s bytes=%-7d final=%s\n", $u, $f['status'], strlen($f['html']), $f['final_url']);
    if ($f['error']) {
        echo "    error: {$f['error']}\n";
    }
    foreach (['server', 'x-shopify-stage', 'x-shopid', 'x-shardid', 'x-powered-by', 'cf-mitigated', 'retry-after', 'content-type'] as $h) {
        if (isset($f['headers'][$h])) {
            echo "    $h: {$f['headers'][$h]}\n";
        }
    }
    if ($f['cookies']) {
        echo '    cookies: ' . implode(', ', array_slice($f['cookies'], 0, 8)) . "\n";
    }
    if ($f['html'] !== '') {
        echo '    shopify markers: '
            . (stripos($f['html'], 'cdn.shopify.com') !== false ? 'cdn.shopify.com ' : '')
            . (stripos($f['html'], 'myshopify.com') !== false ? 'myshopify.com ' : '')
            . (stripos($f['html'], 'Shopify.theme') !== false ? 'Shopify.theme ' : '')
            . "\n";
    }
    echo "\n";
}
