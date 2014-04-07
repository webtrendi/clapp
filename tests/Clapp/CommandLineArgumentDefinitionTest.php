<?php
/**
 * Tests for CommandLineArgumentDefinition
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */

class TestCommandLineArgumentDefinition extends \Clapp\CommandLineArgumentDefinition {

    /**
     * make parseDefinitions publicly callable
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function parseDefinitions() {
        parent::parseDefinitions();
    } // parseDefinitions()

} //TestCommandLineArgumentDefinition


/**
 * Tests for CommandLineArgumentDefinition
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */
class CommandLineArgumentDefinitionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    private $defaultOptions = array();
    
    /**
     * setup some defaults
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function setUp() {
        $this->defaultOptions = array(
            'help|h' => 'help',
            'count|c=int' => 'count',
            'keyword|k=str' => 'keyword',
            'verbose|v+' => 'verbose',
            'exclude|x=str+' => 'exclude',
            'year|y=integer' => 'year',
            'day|d=i' => 'day',
            'month|m=s' => 'month',
            'include|i=string' => 'include',
        );
    } // setUp()
    

    /**
     * test we can get a param using it's long name.
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testParamExistsUsingLongName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertTrue($def->paramExists('help'));
        $this->assertTrue($def->paramExists('count'));
        $this->assertTrue($def->paramExists('keyword'));
        $this->assertTrue($def->paramExists('verbose'));
        $this->assertTrue($def->paramExists('exclude'));
    } // testParamExistsUsingLongName()
    
    /**
     * test we can get a param using it's short name.
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testParamExistsUsingShortName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertTrue($def->paramExists('h'));
        $this->assertTrue($def->paramExists('c'));
        $this->assertTrue($def->paramExists('k'));
        $this->assertTrue($def->paramExists('v'));
        $this->assertTrue($def->paramExists('x'));
    } // testParamExistsUsingShortName()

    /**
     * test that params that allow value reutnrs the right status when using it's long name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testAllowValueUsingLongName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertFalse($def->allowsValue('help'));
        $this->assertTrue($def->allowsValue('count'));
        $this->assertTrue($def->allowsValue('keyword'));
        $this->assertFalse($def->allowsValue('verbose'));
        $this->assertTrue($def->allowsValue('exclude'));

        $this->assertFalse($def->allowsValue('nonExistantParam'));
    } // testAllowValueUsingLongName()

    /**
     * test that params that allow value reutnrs the right status when using it's short name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testAllowValueUsingShortName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertFalse($def->allowsValue('h'));
        $this->assertTrue($def->allowsValue('c'));
        $this->assertTrue($def->allowsValue('k'));
        $this->assertFalse($def->allowsValue('v'));
        $this->assertTrue($def->allowsValue('x'));

        $this->assertFalse($def->allowsValue('z'));
    } // testAllowValueUsingShortName()

    /**
     * test that params that allow multiple instances reutnrs the right status when using it's long name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testAllowMultipleUsingLongName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertFalse($def->allowsMultiple('help'));
        $this->assertFalse($def->allowsMultiple('count'));
        $this->assertFalse($def->allowsMultiple('keyword'));
        $this->assertTrue($def->allowsMultiple('verbose'));
        $this->assertTrue($def->allowsMultiple('exclude'));

        $this->assertFalse($def->allowsMultiple('nonExistantParam'));
    } // testAllowMultipleUsingLongName()

    /**
     * test that params that allow multiple instances reutnrs the right status when using it's short name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testAllowMultipleUsingShortName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertFalse($def->allowsMultiple('h'));
        $this->assertFalse($def->allowsMultiple('c'));
        $this->assertFalse($def->allowsMultiple('k'));
        $this->assertTrue($def->allowsMultiple('v'));
        $this->assertTrue($def->allowsMultiple('x'));

        $this->assertFalse($def->allowsMultiple('z'));
    } // testAllowMultipleUsingShortName()

    /**
     * test that params that allow values reutnrs the right type when using it's long name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetValueTypeUsingLongName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertEquals($def->getValueType('help'), '');
        $this->assertEquals($def->getValueType('verbose'), '');
        $this->assertEquals($def->getValueType('count'), 'integer');
        $this->assertEquals($def->getValueType('year'), 'integer');
        $this->assertEquals($def->getValueType('day'), 'integer');
        $this->assertEquals($def->getValueType('keyword'), 'string');
        $this->assertEquals($def->getValueType('exclude'), 'string');

        $this->assertEquals($def->getValueType('nonExistantParam'), '');
    } // testGetValueTypeUsingLongName()

    /**
     * test that params retunrs the right description when using it's short name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetDescriptionUsingShortName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertEquals($def->getDescription('h'), 'help');
        $this->assertEquals($def->getDescription('v'), 'verbose');
        $this->assertEquals($def->getDescription('c'), 'count');
        $this->assertEquals($def->getDescription('y'), 'year');
        $this->assertEquals($def->getDescription('d'), 'day');
        $this->assertEquals($def->getDescription('k'), 'keyword');
        $this->assertEquals($def->getDescription('x'), 'exclude');
        $this->assertEquals($def->getDescription('m'), 'month');
        $this->assertEquals($def->getDescription('i'), 'include');

        $this->assertNull($def->getDescription('z'));
    } // testGetDescriptionUsingShortName()


    /**
     * test that params retunrs the right description when using it's long name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetDescriptionUsingLongName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertEquals($def->getDescription('help'), 'help');
        $this->assertEquals($def->getDescription('verbose'), 'verbose');
        $this->assertEquals($def->getDescription('count'), 'count');
        $this->assertEquals($def->getDescription('year'), 'year');
        $this->assertEquals($def->getDescription('day'), 'day');
        $this->assertEquals($def->getDescription('keyword'), 'keyword');
        $this->assertEquals($def->getDescription('exclude'), 'exclude');

        $this->assertNull($def->getDescription('nonExistantParam'));
    } // testGetDescriptionUsingLongName()

    /**
     * test that params that allow values reutnrs the right type when using it's short name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetValueTypeUsingShortName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertEquals($def->getValueType('h'), '');
        $this->assertEquals($def->getValueType('v'), '');
        $this->assertEquals($def->getValueType('c'), 'integer');
        $this->assertEquals($def->getValueType('y'), 'integer');
        $this->assertEquals($def->getValueType('d'), 'integer');
        $this->assertEquals($def->getValueType('k'), 'string');
        $this->assertEquals($def->getValueType('x'), 'string');
        $this->assertEquals($def->getValueType('m'), 'string');
        $this->assertEquals($def->getValueType('i'), 'string');

        $this->assertEquals($def->getValueType('z'), '');
    } // testGetValueTypeUsingShortName()

    /**
     * test that that the right short name is returned when using the long name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetShortNameUsingLongName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertEquals($def->getShortName('help'), 'h');
        $this->assertEquals($def->getShortName('verbose'), 'v');
        $this->assertEquals($def->getShortName('count'), 'c');
        $this->assertEquals($def->getShortName('year'), 'y');
        $this->assertEquals($def->getShortName('day'), 'd');
        $this->assertEquals($def->getShortName('keyword'), 'k');
        $this->assertEquals($def->getShortName('exclude'), 'x');

        $this->assertNull($def->getShortName('nonExistantParam'));
    } // testGetShortNameUsingLongName()

    /**
     * test that that the right long name is returned when using the short name
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetLongNameUsingShortName() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertEquals($def->getLongName('h'), 'help');
        $this->assertEquals($def->getLongName('v'), 'verbose');
        $this->assertEquals($def->getLongName('c'), 'count');
        $this->assertEquals($def->getLongName('y'), 'year');
        $this->assertEquals($def->getLongName('d'), 'day');
        $this->assertEquals($def->getLongName('k'), 'keyword');
        $this->assertEquals($def->getLongName('x'), 'exclude');
        $this->assertEquals($def->getLongName('m'), 'month');
        $this->assertEquals($def->getLongName('i'), 'include');

        $this->assertNull($def->getLongName('z'));
    } // testGetLongNameUsingShortName()

    /**
     * test that get usage is not empty
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testGetUsageNotEmpty() {
        $def = new \Clapp\CommandLineArgumentDefinition($this->defaultOptions);

        $this->assertTrue(strlen($def->getUsage()) > 0 );
    } // testGetLongNameUsingShortName()

    /**
     * test that definitions with no short name are not allowed
     *
     * @expectedException UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testInvalidDefinitionWithNoShortName() {
        $def = new TestCommandLineArgumentDefinition(array(
            'help' => 'help'
        ));

        $def->parseDefinitions();
    } // testInvalidDefinitionWithNoShortName()

    /**
     * test that short name are exactly one char
     *
     * @expectedException UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testInvalidDefinitionWithShortNameNotSingleChar() {
        $def = new TestCommandLineArgumentDefinition(array(
            'help|ho' => 'help'
        ));

        $def->parseDefinitions();
    } // testInvalidDefinitionWithShortNameNotSingleChar()
    
    /**
     * test that invalid value types are not allowed
     *
     * @expectedException UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testInvalidDefinitionWithNonExistantValueType() {
        $def = new TestCommandLineArgumentDefinition(array(
            'year|y=bool' => 'year'
        ));

        $def->parseDefinitions();
    } // testInvalidDefinitionWithNonExistantValueType()

    /**
     * test that the allow multiple char is only allowed as last char
     *
     * @expectedException UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testInvalidDefinitionMisplacedMultipleChar() {
        $def = new TestCommandLineArgumentDefinition(array(
            'help|h+o' => 'help'
        ));

        $def->parseDefinitions();
    } // testInvalidDefinitionWithShortNameNotSingleChar()

    
    /**
     * test that the you cannot redeclare a long name
     *
     * @expectedException UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testInvalidDefinitionDuplicateLongName() {
        $def = new TestCommandLineArgumentDefinition(array(
            'help|h' => 'help',
            'help|v' => 'help2',
        ));

        $def->parseDefinitions();
    } // testInvalidDefinitionDuplicateLongName()

    /**
     * test that the you cannot redeclare a short name
     *
     * @expectedException UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testInvalidDefinitionDuplicateShortName() {
        $def = new TestCommandLineArgumentDefinition(array(
            'help|h' => 'help',
            'verbose|h' => 'verbose',
        ));

        $def->parseDefinitions();
    } // testInvalidDefinitionDuplicateShortName()

} // CommandLineArgumentDefinitionTest class
