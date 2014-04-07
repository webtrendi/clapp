<?php
/**
 * Defines list and formats of command line arguments
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */

namespace Clapp;

/**
 * Defines list and formats of command line arguments
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */
class CommandLineArgumentDefinition
{

    /**
     * @var array
     */
    private $definitions = array();

    /**
     * long names as keys and array of properties as values
     *
     * properties are as follows
     * * string "shortName" one letter char to the corresponding short name
     * * boolean "isMultipleAllowed" true if mutliple instances of the param are allowed 
     * * mixed "parameterType" false if paramters are not alloweda value, otherwise a string with the value "integer" or "string"
     * * string "description" description of the parameter 
     * @var array
     */
    private $longNames = array();
    
    /**
     * list of short names as keys and their long name equivalent as values
     * @var array
     */
    private $shortNames = array();

    /**
     * Flag if arguments have been parsed in to params
     * @var boolean
     */
    private $isParsed = false;

    /**
     * class constructor
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param array $definitions contains list of allowed parameters
     *     the key is the long name of the parameter followed by a pipe (|)
     *     then a single character specifying the short name. 
     *     
     *     If the parameter allows for arguments then an equal sign (=)
     *     follows and then the type of paramter. 
     *     
     *     Allowed types are either i, int or integer for integer  types
     *     and s, str or string for string types.
     *
     *     If a parameter can appear more than once the last character of 
     *     the key should be a plus character (+).
     *
     *     The value of the entry is the definition of what the paramter
     *     does.
     */
    public function __construct($definitions) {
        if (is_array($definitions)) {
            $this->definitions = $definitions;
        } //if
    } // __construct()

    /**
     * checks if parameter is allowed
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param string $name either short or long name of the parameter to check
     *
     * @return boolean true if definition exisits, false otherwise
     */
    public function paramExists($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        if (strlen($name) == 1) {
            return isset($this->shortNames[$name]);
        } else {
            return isset($this->longNames[$name]);
        } //if
    } // paramExists($name)
    

    /**
     * checks if parameter allows a value if so what type
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     * 
     * @param string $name either short or long name of the parameter to check
     *
     * @return boolean|string false doesn't allow value, The value "string" or "integer" depending which type it allows
     */
    public function allowsValue($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        $longName = (strlen($name) == 1 ? ( isset($this->shortNames[$name]) ? $this->shortNames[$name] : '') : $name);

        if (isset($this->longNames[$longName])) {
            return $this->longNames[$longName]['parameterType'] !== false ? true : false;
        } else {
            return false;
        } //if
    } // allowsValue()
    
    /**
     * returns the type of value allowed
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function getValueType($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        $longName = (strlen($name) == 1 ? ( isset($this->shortNames[$name]) ? $this->shortNames[$name] : '') : $name);

        if (isset($this->longNames[$longName]['parameterType']) && $this->longNames[$longName]['parameterType'] !== false) {
            return $this->longNames[$longName]['parameterType'];
        } else {
            return '';
        } //if
    } // getValueType()
    

    /**
     * checks if pamultiple instance of parameter are allowed
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     * 
     * @param string $name either short or long name of the parameter to check
     *
     * @return boolean false if parameter doesn't allow multiple values, true if it does
     */
    public function allowsMultiple($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        $longName = (strlen($name) == 1 ? ( isset($this->shortNames[$name]) ? $this->shortNames[$name] : '') : $name);

        if (isset($this->longNames[$longName])) {
            return $this->longNames[$longName]['isMultipleAllowed'];
        } else {
            return false;
        } //if
    } // allowsMultiple()

    /**
     * retreive short name of a parameter using its long name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param string $name long name of the parameter to check
     *
     * @return string character of the short name or null if it doesn't exist
     */
    public function getShortName($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        if (isset($this->longNames[$name])) {
            return $this->longNames[$name]['shortName'];
        } else {
            return null;
        } //if
    } // getShortName($name)
    
    /**
     * retreive long name of a parameter using its short name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param string $name short name of the parameter to check
     *
     * @return string long name or null if it doesn't exist
     */
    public function getLongName($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        if (isset($this->shortNames[$name])) {
            return $this->shortNames[$name];
        } else {
            return null;
        } //if
    } // getLongName($name)

    /**
     * retreive description of a paramter
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     *
     * @param string $name either short or long name of the parameter to check
     *
     * @return string description or null if it doesn't exist
     */
    public function getDescription($name) {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        $longName = (strlen($name) == 1 ? ( isset($this->shortNames[$name]) ? $this->shortNames[$name] : '') : $name);

        if (isset($this->longNames[$longName])) {
            return $this->longNames[$longName]['description'];
        } else {
            return null;
        } //if
    } // getDescription()
    
    /**
     * builds a usage definition based on definition of params
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function getUsage() {
        if (!$this->isParsed) {
            $this->parseDefinitions();
        } //if

        /* build list of argument names and calculate 
           the first column width so we can pad to 
           align definitions */
        $firstCol = array();
        $longestDef = 0;
        foreach (array_keys($this->longNames) as $longName) {
            ob_start();
            echo "--{$longName}|-{$this->getShortName($longName)}";

            if ($this->allowsValue($longName)) {
                echo "={$this->getValueType($longName)}";
            } //if

            if ($this->allowsMultiple($longName)) {
                echo "+";
            } //if

            $defLenght = ob_get_length();

            $longestDef = max($longestDef, ob_get_length());

            $firstCol[$longName] = ob_get_contents();
            ob_end_clean();

        } //foreach

        $firstColMaxWidth = $longestDef + 4;

        ob_start();

        foreach ($firstCol as $longName => $def) {
            $currentDefLength = strlen($def);

            $padding = str_repeat(" ", $firstColMaxWidth - $currentDefLength);

            echo "{$def}{$padding}{$this->getDescription($longName)}", PHP_EOL;
        } //foreach

        echo PHP_EOL;

        $usage = ob_get_contents();
        ob_end_clean();
        
        return $usage;

    } // getUsage()
    

    /**
     * parses the definitions
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    protected function parseDefinitions() {
        foreach ($this->definitions as $nameDef => $description) {
            $nameParts = explode("|", $nameDef);

            if (sizeof($nameParts) !== 2) {
                throw new \UnexpectedValueException("Unexpected argument name definition expecting \"longName|char\"");
            } //if

            $longName = $nameParts[0];
            $isMulti = false;
            $parameterType = false;

            $shortNameLength = strlen($nameParts[1]);

            if ($shortNameLength == 1) {
                $shortName = $nameParts[1];
            } else {
                $secondChar = substr($nameParts[1], 1,1);

                switch ($secondChar) {
                    case '=':
                        $shortNameParts = explode("=", $nameParts[1]);

                        $shortName = $shortNameParts[0];
                        $parameterTypeString = $shortNameParts[1];

                        if (substr($parameterTypeString, -1) === '+') {
                            $isMulti = true;
                            $parameterTypeString = substr($parameterTypeString, 0, -1); // remove trailing +
                        } //if

                        switch ($parameterTypeString) {
                            case 'i':
                            case 'int':
                            case 'integer':
                                $parameterType = 'integer';
                                break;
                            case 's':
                            case 'str':
                            case 'string':
                                $parameterType = 'string';
                                break;
                            default:
                                throw new \UnexpectedValueException("Expecting parameter type to be either integer or string");
                                break;
                        } //switch

                        break;
                    case '+':
                        if ($shortNameLength > 2) {
                            throw new \UnexpectedValueException("Multiple flag charachter (+) should be last character in definition");
                        } //if

                        $shortName = substr($nameParts[1], 0,1);
                        $isMulti = true;

                        break;
                    default:
                        throw new \UnexpectedValueException("Expecting short name definition to be a single char");
                        break;
                } // switch

            } //if

            if (isset($this->longNames[$longName])) {
                throw new \UnexpectedValueException("Cannot redefine long name {$longName}");
            } //if

            if (isset($this->shortNames[$shortName])) {
                throw new \UnexpectedValueException("Cannot redefine short name {$shortName}");
            } //if

            $this->longNames[$longName] = array(
                'shortName' => $shortName,
                'isMultipleAllowed' => $isMulti,
                'parameterType' => $parameterType,
                'description' => $description
            );

            $this->shortNames[$shortName] = $longName;

        } //foreach

        $this->isParsed = true;
    } // parseDefinitions()
    
} // CommandLineArgumentDefinition class
