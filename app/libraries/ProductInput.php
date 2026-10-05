<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductInput
{
    public function validate(array $input, bool $partial = false): array
    {
        $data = [];
        $errors = [];
        foreach (['product_name', 'description', 'price', 'quantity'] as $field) {
            if ($partial && !array_key_exists($field, $input)) continue;
            $value = $input[$field] ?? null;
            if (!is_string($value) && !is_int($value) && !is_float($value)) {
                $errors[$field] = 'This field is required and must contain a valid value.';
                continue;
            }
            $value = trim((string) $value);
            if ($field === 'product_name' || $field === 'description') {
                $max = $field === 'product_name' ? 100 : 10000;
                $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
                if ($value === '' || $length > $max) $errors[$field] = "Enter between 1 and {$max} characters.";
                else $data[$field] = $value;
            } elseif ($field === 'price') {
                if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $value)) {
                    $errors[$field] = 'Enter a price from 0 to 99,999,999.99 with up to two decimal places.';
                } else $data[$field] = number_format((float) $value, 2, '.', '');
            } else {
                if (!preg_match('/^\d+$/D', $value) || (float) $value > 2147483647) {
                    $errors[$field] = 'Enter a whole quantity from 0 to 2,147,483,647.';
                } else $data[$field] = (int) $value;
            }
        }
        if ($partial && !$data && !$errors) $errors['form'] = 'Provide at least one product field to update.';
        return ['data' => $data, 'errors' => $errors];
    }
}
