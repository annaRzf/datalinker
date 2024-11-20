<?php

trait DataLinkeRHelpers
{
    public function map_array_keys($data, $mapping)
    {
        $mapped_data = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Recursively map keys for nested arrays
                $mapped_data[$key] = $this->map_array_keys($value, $mapping);
            } else {
                if (array_key_exists($key, $mapping)) {
                    $mapped_data[$mapping[$key]] = $value;
                } else {
                    $mapped_data[$key] = $value;
                }
            }
        }

        return $mapped_data;
    }

    function dl_compare_post_types( $a, $b ) {
		return strcmp( $a->labels->name, $b->labels->name );
	}

    public function log_data($data)
    {
        error_log(print_r($data, true));
    }
    
}