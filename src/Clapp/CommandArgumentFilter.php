<?php
/**
 * Filters an array and extracts and validates command line arguments
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */

namespace Clapp;

/**
 * Filters an array and extracts and validates command line arguments
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */
class CommandArgumentFilter
{
    /**
     * Command line arguments
     * @var array
     */
    private $arguments = array();

    /**
     * Additional options
     * @var array
     */
    private $options = array();
    
    /**
     * Definition of allowed parameters
     * @var \Clapp\CommandLineArgumentDefinition
     */
    private $definitions = null;

    /**
     * Flag if arguments have been parsed in to params
     * @var boolean
     */
    private $parsed = false;

    /**
     * Parsed params
     * @var array
     */
    private $params = array();

    /**
     * Trailing values
     * @var string
     */
    private $trailingValues = "";

    /**
     * Floating values
     * @var array
     */
    private $floatingValues = array();
    
    /**
     * program name
     * @var string
     */
    private $programName = "";

    /**
     * class constructor
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param \Clapp\CommandLineDefinition $definitions contains list of allowed parameters
     * @param array $args list of arguments to filter.
     * @param array $options additional options
     */
    public function __construct(\Clapp\CommandLineArgumentDefinition $definitions, $args, $options = array())
    {
        if (is_array($args)) {
            $this->arguments = $args;
        } //if

        $this->options = array_merge(array(
          'allowFloatingValues' => false,
          'ignoreUnknownOptions' => false
        ), $options);

        $this->definitions = $definitions;
    } // __construct()

    /**
     * returns parameter matching provided name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param string name of the paramter to retreive
     *
     * @return mixed if param the param appears only once the method will
     *     return 1 if the parameter doesn't take a value. The specified value
     *     for that param will returned if it does take value.
     *
     *     If many occurence of the param appear the number of occurences will
     *     be returned for params that do not take values. An array of values
     *     will be returned for the parameters that do take values.
     *
     *     If the parameter is not present null if it takes a value and false if
     *     it's not present and doesn't allow values
     */
    public function getParam($name)
    {
        if (!$this->parsed) {
            $this->parseParams();
        } //if

        $longName = strlen($name) === 1 ? $this->definitions->getLongName($name) : $name;
        if (isset($this->params[$longName])) {
            return $this->params[$longName];
        } else {
            if ($this->definitions->allowsValue($longName)) {
                return null;
            } else {
                return false;
            } //if
        } //if

    } // getParam()

    /**
     * retreive the program name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function getProgramName()
    {
        if (!$this->parsed) {
            $this->parseParams();
        } //if

        return $this->programName;
    } // getProgramName()
    
    /**
     * retreive the trailing values
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function getTrailingValues()
    {
        if (!$this->parsed) {
            $this->parseParams();
        } //if

        return $this->trailingValues;
    } // getTrailingValues()

    /**
     * retreive the floating values
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function getFloatingValues()
    {
        if (!$this->parsed) {
            $this->parseParams();
        } //if

        return $this->floatingValues;
    } // getFloatingValues()

    /**
     * extracts params from arguments
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    protected function parseParams()
    {

        $argumentStack = $this->arguments;

        $expectingValue = false;
        $currentLongName = null;
        $currentValue = null;
        $trailingValues = "";
        $endOfDashedArguments = false;
        $addParam = false;
        $argumentsLeft = sizeof($argumentStack);
        $multiShortParams = array();

        $this->programName = array_shift($argumentStack); // remove first argument which is the program name

        while ($currentArgument = array_shift($argumentStack)) {
            $argumentsLeft--;
            $currentArgumentLength = strlen($currentArgument);

            // arguments that don't start with a dash
            if (substr($currentArgument, 0, 1) !== '-') {
                if ($expectingValue) {
                    $currentValue = $currentArgument;
                    $addParam = true;
                } else if ($this->options['allowFloatingValues']) {
                  $this->floatingValues[] = $currentArgument;
                } else {
                    $trailingValues .= " ". $currentArgument;
                    $endOfDashedArguments = true;
                } //if

            // double dash detected
            } elseif (substr($currentArgument, 1, 1)  === '-') {
                if ($expectingValue) {
                    throw new \UnexpectedValueException("Parameter {$currentLongName} expects a values");
                } //if

                /* stop parsing arguments if double dash
                   only param is encountered  */
                if ($currentArgumentLength == 2) {
                    if ($trailingValues !== "") {
                        throw new \UnexpectedValueException("Trailing values must appear after double dash");
                    } //if

                    $trailingValues = " ". implode(" ", $argumentStack);
                    $argumentStack = array();
                    $endOfDashedArguments = true;
                    break;
                } //if

                $longNameParts = explode("=", substr($currentArgument, 2), 2);

                $currentLongName = $longNameParts[0];

                if (sizeof($longNameParts) > 1) {
                    $currentValue = $longNameParts[1];
                    $addParam = true;
                } elseif ($this->definitions->allowsValue($currentLongName)) {
                    $expectingValue = true;
                } else {
                    $addParam = true;
                } //if

            // single dash
            } else {
                if ($expectingValue) {
                    throw new \UnexpectedValueException("Parameter {$currentLongName} expects a values");
                } //if

                $shortNameParts = explode("=", substr($currentArgument, 1), 2);

                $shortName = $shortNameParts[0];

                if (strlen($shortName) <= 1) {
                    $currentLongName = $this->definitions->getLongName($shortName);

                    if ($currentLongName === null) {
                      if ($this->options['ignoreUnknownOptions']) {
                        // set parameter name to a value that can't be provided by the user
                        $currentLongName = NAN;
                      } else {
                        throw new \InvalidArgumentException("Unable to find name with ".
                            "provided parameter ({$shortName})");
                      } // if
                    } //if

                    if (sizeof($shortNameParts) > 1) {
                        $currentValue = $shortNameParts[1];
                        $addParam = true;
                    } elseif ($this->definitions->allowsValue($currentLongName)) {
                        $expectingValue = true;
                    } else {
                        $addParam = true;
                    } //if

                } else {
                    $multiShortParams = str_split($shortName);

                    /* process the last one (which is the only one that can have a value) */
                    $lastParam = array_pop($multiShortParams);
                    $currentLongName = $this->definitions->getLongName($lastParam);
                    if (sizeof($shortNameParts) > 1) {
                        $currentValue = $shortNameParts[1];
                        $addParam = true;
                    } elseif ($this->definitions->allowsValue($lastParam)) {
                        $expectingValue = true;
                    } else {
                        $addParam = true;
                    } //if

                } //if
                
            } //if

            if ($addParam) {
                if ($endOfDashedArguments) {
                    throw new \UnexpectedValueException("Unexpected argument after undashed values");
                } //if

                /* Not sure how this could happen */
                // @codeCoverageIgnoreStart
                if ($currentLongName === false || $currentLongName === null) {
                    throw new \UnexpectedValueException("Missing argument name");
                } //if
                // @codeCoverageIgnoreEnd

                if (!$this->options['ignoreUnknownOptions']) {
                  if (!$this->definitions->paramExists($currentLongName)) {
                    throw new \InvalidArgumentException("Invalid argument name");
                  } //if
                } //if

                $allowsMultiple = $this->definitions->allowsMultiple($currentLongName);
                $allowsValue = $this->definitions->allowsValue($currentLongName);

                if (isset($this->params[$currentLongName]) && !$allowsMultiple && !is_nan($currentLongName)) {
                    throw new \UnexpectedValueException("Multiple instace of parameter {$currentLongName} not allowed");
                } //if

                if ($allowsValue) {
                    /* Missing values should always be detected before addParam is true */
                    // @codeCoverageIgnoreStart
                    if ($currentValue === null) {
                        throw new \UnexpectedValueException("Parameter {$currentLongName} expects a values");
                    } //if
                    // @codeCoverageIgnoreEnd

                } elseif ($currentValue !== null) {
                    throw new \UnexpectedValueException("Parameter {$currentLongName} does not accept values");

                } else {
                    $currentValue = true;
                } //if

                if ($allowsMultiple) {
                    if ($allowsValue) {
                        if (!isset($this->params[$currentLongName])) {
                            $this->params[$currentLongName] = array();
                        } //if

                        $this->params[$currentLongName][] = $currentValue;

                    } else {
                        if (!isset($this->params[$currentLongName])) {
                            $this->params[$currentLongName] = 0;
                        } //if

                        $this->params[$currentLongName]++;

                    } //if

                } else {
                    $this->params[$currentLongName] = $currentValue;
                } //if

                foreach ($multiShortParams as $shortName) {
                    $argumentStack[] = "-{$shortName}";
                    $argumentsLeft++;
                } //foreach

                /* reset stuff for next param */
                $expectingValue = false;
                $currentLongName = null;
                $currentValue = null;
                $addParam = false;
                $multiShortParams = array();

            } //if

        } //while

        if ($expectingValue !== false) {
            throw new \UnexpectedValueException("Parameter {$currentLongName} expects a values");
        } //if

        /* Not sure how this could happen */
        // @codeCoverageIgnoreStart
        if ($currentLongName !== null ||
            $addParam !== false ||
            $currentValue !== null ||
            sizeof($multiShortParams) !== 0) {
            throw new \UnexpectedValueException("Unable to process some parameters");
        } //if
        // @codeCoverageIgnoreEnd

        if ($trailingValues !== "") {
            $this->trailingValues = substr($trailingValues, 1); // remove extra space at the begging
        } //if

        $this->parsed = true;
    } // parseParams()
}
