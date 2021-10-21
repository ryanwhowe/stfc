<?php

namespace App\Stfc\Utils;

class StringUtils {

    public static function reduceIds(array $source): array {
        $results = [];
        array_walk_recursive($source, function($v, $k) use (&$results){
            if($k === 'id'){
                if(isset($results[$v])){
                    $results[$v]++;
                } else {
                    $results[$v] = 1;
                }
            }
        });
        return $results;
    }

    public static function reduceTopLevelIds(array $source): array {
        $results = [];
        foreach ($source as $value) {
            array_walk($value, function($v, $k) use (&$results){
                if($k === 'id'){
                    if(isset($results[$v])){
                        $results[$v]++;
                    } else {
                        $results[$v] = 1;
                    }
                }
            });
        }
        return $results;
    }

}