<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('flattenArray')) {
    /**
     * Flatten multidimensional to one dimensional array
     *
     * @param array $input
     * @param array $output
     * @return array $output
     */
    function flattenArray(array $input, array &$output)
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                flattenArray($value, $output);
            } else {
                $output[$key][] = $value;
            }
        }
    }
}
