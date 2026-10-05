<?php
define('PREVENT_DIRECT_ACCESS', true);
require dirname(__DIR__) . '/app/libraries/ProductInput.php';
$validator = new ProductInput();
$valid = ['product_name' => 'Nike Air Force 1', 'description' => 'Classic sneakers', 'price' => '5995.00', 'quantity' => '12'];
function check($value, $message) { if (!$value) { fwrite(STDERR, $message . "\n"); exit(1); } }
check(!$validator->validate($valid)['errors'], 'Valid product was rejected');
foreach ([['price', '-1'], ['price', '1.234'], ['price', '1e4'], ['price', '100000000'],
    ['quantity', '-1'], ['quantity', '1.5'], ['quantity', '2147483648'], ['product_name', str_repeat('a', 101)],
    ['description', []], ['price', true]] as [$field, $value]) {
    check(isset($validator->validate(array_replace($valid, [$field => $value]))['errors'][$field]), "Invalid {$field} was accepted");
}
check($validator->validate(['quantity' => '0'], true)['data'] === ['quantity' => 0], 'PATCH did not preserve omitted fields');
check(isset($validator->validate([], true)['errors']['form']), 'Empty PATCH was accepted');
echo "PASS: Lab 6 product input validation.\n";
