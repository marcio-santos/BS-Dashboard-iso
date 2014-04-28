<?php
/**
 * Created by PhpStorm.
 * User: MárcioAlex
 * Date: 15/04/14
 * Time: 11:00
 */

function getConfig() {
    $config_file = file_get_contents('config.json');
    $output = json_decode($config_file) ;
    return $output;

}

function setConfig($block) {

    foreach ($block as $key => $value) {


    }


}