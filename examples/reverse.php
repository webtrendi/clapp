#!/usr/bin/php
<?php
/**
 * Reverse the text of a given message
 * 
 * @since  2014-04-04
 * @author Patrick Forget <patforg@geekpad.ca>
 */

/* Composer autloader  */
include "../vendor/autoload.php";

$definitions = new \Clapp\CommandLineArgumentDefinition(
    array(
        "help|h"            => "Shows help message",
        "message|m=s"       => "Original message",
        "character-count|c" => "Also print character count",
    )
);

$filter = new \Clapp\CommandArgumentFilter($definitions, $argv);

if ($filter->getParam('h') === true) {
    fwrite(STDERR, $definitions->getUsage());
    exit(0);
} //if

$message = $filter->getParam('m');

/* Show reverse message if a message was provided */
if ($message !== null) {
    echo strrev($message), PHP_EOL;
} //if


/* Show character count */
if ($filter->getParam("character-count") !== false) {
    
    echo "Message length: ";
    echo ($message === null ? 0 : strlen($message) );
    echo PHP_EOL;

} //if
