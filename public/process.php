<?php

require "../vendor/autoload.php";

use Testing\TokenHandler;
use Testing\ShippingGenerator;

$options = getopt("i:s:e:p:v:q:",["q::"]);
$quantity = $options['q'] ?? 1;
if (!$quantity) {
    $quantity = 1;
}

$prefix = substr($options['e'], 0, 8);
$tokenHandler = new TokenHandler($options['i'], $options['s'], $options['e'], $options['p']);
$shippingGenerator = new ShippingGenerator();

$pdeList = [
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    '7'
];

for ($i=1; $i<=$quantity; $i++) {

    try {
        $tokenInit = new DateTime();
        $token = $tokenHandler->getToken();
        $tokenEnd = new DateTime();
        $tokenElapsedTime = $tokenInit->diff($tokenEnd)->s;

        $shippingInit = new DateTime();
        $shipping = $shippingGenerator->makeShipping($token, $seller = $options['v'], $pde = $pdeList[rand(0, max(array_keys($pdeList)))]);
        $shippingEnd = new DateTime();
        $shippingElapsedTime = $shippingInit->diff($shippingEnd)->s;
        error_log(
            "{$prefix} {$shipping['tracking_nro']}: (token: {$tokenElapsedTime} segundos - shipping: {$shippingElapsedTime} segundos)",
            3,
            '/home/diego/code/stress-shipping/public/error.log'
        );
    } catch (Exception $e) {
        error_log(
            $prefix . "No se pudo realizar shipping {$i} \n",
            3,
            '/home/diego/code/stress-shipping/public/error.log'
        );

        continue;
    }
}
