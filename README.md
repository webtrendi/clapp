# CLAPP: Command Line Argument Parser For PHP 
[![Build Status](https://travis-ci.org/webtrendi/clapp.svg?branch=master)](https://travis-ci.org/webtrendi/clapp)

Parses command line arguments passed to a PHP script.  

## Usage

```PHP

// Define accepted arguments
$definitions = new \Clapp\CommandLineArgumentDefinition(array(
    "help|h"      => "Shows help message",
    "message|m=s" => "Input message",
    "verbose|v+"  => "Set level of verbose output",
));

// Filter arguments based and validate according to definitions
$filter = new \Clapp\CommandArgumentFilter($definitions, $argv);

// Retrieve parameter if set
if ($filter->getParam('h') !== false) {
    echo $definitions->getUsage();
    exit();
} //if
```
## Features

* Simple definition syntax 
* Supports long and short arugment names (-v --verbose)
* Supports repeated arguments (--name=bob --name=mary)
* Supports multiple short arguments (-abc equivalent of -a -b -c)
* Supports 2 ways of setting values (--name=bob or --name bob)
* Supports double dash delimiter for trailing values
* Creates detailed usage documentation using definitions

