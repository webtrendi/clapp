<?php
/**
 * Test for CommandArgumentFilter
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */

class TestCommandArgumentFilter extends \Clapp\CommandArgumentFilter {

    /**
     * make parseParams publicly callable
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function parseParams() {
        parent::parseParams();
    } // parseParams()

} //TestCommandLineArgumentDefinition

/**
 * Test for CommandArgumentFilter
 *
 * @author Patrick Forget <patforg at webtrendi.com>
 */
class CommandArgumentFilterTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var array
     */
    private $defaultDefinition = array();
    
    /**
     * setup some defaults
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function setUp() {
        $this->defaultDefinition = new \Clapp\CommandLineArgumentDefinition(array(
            'help|h' => 'help',
            'count|c=int' => 'count',
            'keyword|k=str' => 'keyword',
            'verbose|v+' => 'verbose',
            'exclude|x=str+' => 'exclude',
            'year|y=integer' => 'year',
            'day|d=i' => 'day',
            'month|m=s' => 'month',
            'include|i=string' => 'include',
        ));
    } // setUp()

    /**
     * test that short name options with no values are detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithNoValueExists() {
        $argv = explode(" ", './test.php -h');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertTrue($argFilter->getParam('h'));
        $this->assertTrue($argFilter->getParam('help'));

    } // testShortNameWithNoValueExists()
    
    /**
     * test that long name options with no values are detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithNoValueExists() {
        $argv = explode(" ", './test.php --help');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertTrue($argFilter->getParam('h'));
        $this->assertTrue($argFilter->getParam('help'));

    } // testLongNameWithNoValueExists()

    /**
     * test that short name options with values are detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithValueSetWithEqualSignExists() {
        $argv = explode(" ", './test.php -c=8');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('c'), 8);
        $this->assertEquals($argFilter->getParam('count'), 8);

    } // testShortNameWithValueSetWithEqualSignExists()
    
    /**
     * test that long name options with values are detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithValueSetWithEqualSignExists() {
        $argv = explode(" ", './test.php --count=8');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('c'),8);
        $this->assertEquals($argFilter->getParam('count'),8);

    } // testLongNameWithValueSetWithEqualSignExists()

    /**
     * test that short name options with values are detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithValueSetWithSpaceExists() {
        $argv = explode(" ", './test.php -c 8');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('c'), 8);
        $this->assertEquals($argFilter->getParam('count'), 8);

    } // testShortNameWithValueSetWithSpaceExists()
    
    /**
     * test that long name options with values are detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithValueSetWithSpaceExists() {
        $argv = explode(" ", './test.php --count 8');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('c'),8);
        $this->assertEquals($argFilter->getParam('count'),8);

    } // testLongNameWithValueSetWithSpaceExists()


    /**
     * test that multiple declarations of short name options with no values are counted
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithNoValueCountMultipleInstanceOfParameter() {
        $argv = explode(" ", './test.php -v -v -v');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('v'), 3);
        $this->assertEquals($argFilter->getParam('verbose'), 3);

    } // testShortNameWithNoValueCountMultipleInstanceOfParameter()
    
    /**
     * test that multiple declarations of long name options with no values are counted
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithNoValueCountMultipleInstanceOfParameter() {
        $argv = explode(" ", './test.php --verbose --verbose --verbose');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('v'), 3);
        $this->assertEquals($argFilter->getParam('verbose'), 3);

    } // testLongNameWithNoValueCountMultipleInstanceOfParameter()

    /**
     * test that multiple declarations of short name options with values are captured
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithValueSetWithEqualSignCapturesMultipleInstanceOfParameter() {
        $argv = explode(" ", './test.php -x=me -x=you -x=them');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals(sizeof($argFilter->getParam('x')), 3);
        $this->assertEquals(sizeof($argFilter->getParam('exclude')), 3);

        $this->assertEquals($argFilter->getParam('x')[2], "them");
        $this->assertEquals($argFilter->getParam('exclude')[2], "them");

    } // testShortNameWithValueSetWithEqualSignCapturesMultipleInstanceOfParameter()
    
    /**
     * test that multiple declarations of long name options with values are captured
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithValueSetWithEqualSignCapturesMultipleInstanceOfParameter() {
        $argv = explode(" ", './test.php --exclude=me --exclude=you --exclude=them');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals(sizeof($argFilter->getParam('x')), 3);
        $this->assertEquals(sizeof($argFilter->getParam('exclude')), 3);

        $this->assertEquals($argFilter->getParam('x')[2], "them");
        $this->assertEquals($argFilter->getParam('exclude')[2], "them");

    } // testLongNameWithValueSetWithEqualSignCapturesMultipleInstanceOfParameter()

    /**
     * test that multiple declarations of short name options with values are captured
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithValueSetSpaceCapturesMultipleInstanceOfParameter() {
        $argv = explode(" ", './test.php -x me -x you -x them');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals(sizeof($argFilter->getParam('x')), 3);
        $this->assertEquals(sizeof($argFilter->getParam('exclude')), 3);

        $this->assertEquals($argFilter->getParam('x')[2], "them");
        $this->assertEquals($argFilter->getParam('exclude')[2], "them");

    } // testShortNameWithValueSetSpaceCapturesMultipleInstanceOfParameter()
    
    /**
     * test that multiple declarations of long name options with values are captured
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithValueSetSpaceCapturesMultipleInstanceOfParameter() {
        $argv = explode(" ", './test.php --exclude me --exclude you --exclude them');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals(sizeof($argFilter->getParam('x')), 3);
        $this->assertEquals(sizeof($argFilter->getParam('exclude')), 3);

        $this->assertEquals($argFilter->getParam('x')[2], "them");
        $this->assertEquals($argFilter->getParam('exclude')[2], "them");

    } // testLongNameWithValueSetSpaceCapturesMultipleInstanceOfParameter()

    /**
     * test that multiple short name options can be declared in one param
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameManyParametersAtOnce() {
        $argv = explode(" ", './test.php -vh');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('v'), 1);
        $this->assertEquals($argFilter->getParam('verbose'), 1);

        $this->assertTrue($argFilter->getParam('h'));
        $this->assertTrue($argFilter->getParam('help'));

    } // testShortNameManyParametersAtOnce()

    /**
     * test that multiple short name options can be declared in one param
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameManyParametersAtOnceWithValueSetWithEqualSign() {
        $argv = explode(" ", './test.php -vhc=8');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('c'), 8);
        $this->assertEquals($argFilter->getParam('count'), 8);

        $this->assertEquals($argFilter->getParam('v'), 1);
        $this->assertEquals($argFilter->getParam('verbose'), 1);

        $this->assertTrue($argFilter->getParam('h'));
        $this->assertTrue($argFilter->getParam('help'));

    } // testShortNameManyParametersAtOnceWithValueSetWithEqualSign()

    /**
     * test that multiple short name options can be declared in one param
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameManyParametersAtOnceWithValueSetWithSpace() {
        $argv = explode(" ", './test.php -vhc 8');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getParam('c'), 8);
        $this->assertEquals($argFilter->getParam('count'), 8);

        $this->assertEquals($argFilter->getParam('v'), 1);
        $this->assertEquals($argFilter->getParam('verbose'), 1);

        $this->assertTrue($argFilter->getParam('h'));
        $this->assertTrue($argFilter->getParam('help'));

    } // testShortNameManyParametersAtOnceWithValueSetWithSpace()

    /**
     * test that short name options with no values are not detected when not present
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testUnspecifiedArgumentNoValueDoesNotExist() {
        $argv = explode(" ", './test.php -v');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertFalse($argFilter->getParam('h'));
        $this->assertFalse($argFilter->getParam('help'));

    } // testUnspecifiedArgumentNoValueDoesNotExist()

    /**
     * test that short name options that accept values are not detected when not present
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testUnspecifiedArgumentThatAcceptValueDoesNotExist() {
        $argv = explode(" ", './test.php -v');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertNull($argFilter->getParam('x'));
        $this->assertNull($argFilter->getParam('exclude'));

    } // testUnspecifiedArgumentThatAcceptValueDoesNotExist()

    /**
     * test that program name is detected
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testProgramNameExtracted() {
        $argv = explode(" ", './test.php -v');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getProgramName(), './test.php');

    } // testProgramNameExtracted()

    /**
     * test that program name is detected when no params are passed
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testProgramNameExtractedWithNoParameters() {
        $argv = explode(" ", './test.php');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getProgramName(), './test.php');

    } // testProgramNameExtractedWithNoParameters()

    /**
     * test single trailing value
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testSingleTrailingValue() {
        $argv = explode(" ", './test.php -v -h trailing');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getTrailingValues(), 'trailing');

    } // testSingleTrailingValue()

    /**
     * test many trailing value
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testMultipleTrailingValue() {
        $argv = explode(" ", './test.php -v -h many trailing');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getTrailingValues(), 'many trailing');

    } // testMultipleTrailingValue()

    /**
     * test single trailing value with double dash
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testSingleTrailingValueWithDoubleDash() {
        $argv = explode(" ", './test.php -v -h -- trailing');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getTrailingValues(), 'trailing');

    } // testSingleTrailingValueWithDoubleDash()

    /**
     * test no value after double dash
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testEmptyValueWithDoubleDash() {
        $argv = explode(" ", './test.php -v -h --');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getTrailingValues(), '');

    } // testEmptyValueWithDoubleDash()

    /**
     * test many trailing value
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testMultipleTrailingValueWithDoubleDash() {
        $argv = explode(" ", './test.php -v -h -- many trailing');

        $argFilter = new \Clapp\CommandArgumentFilter($this->defaultDefinition, $argv);

        $this->assertEquals($argFilter->getTrailingValues(), 'many trailing');

    } // testMultipleTrailingValueWithDoubleDash()

    /**
     * test that there is an exception when you try to have trailing values before a double dash
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testTrailingValueBeforeDoubleDashIsNotAllowed() {
        $argv = explode(" ", './test.php -v -h beforedoubledash -- many trailing');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameWithValueSetWithEqualSignIsNotAllowed()

    /**
     * test that there is an exception when you try to have trailing values between parameters
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testTrailingValueBetweenParametersIsNotAllowed() {
        $argv = explode(" ", './test.php --verbose beforedoubledash --help');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameWithValueSetWithEqualSignIsNotAllowed()


    /**
     * test that there is an exception when you try to set a value to short name options that does not take one
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithValueSetWithEqualSignIsNotAllowed() {
        $argv = explode(" ", './test.php -h=8');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameWithValueSetWithEqualSignIsNotAllowed()
    
    /**
     * test that long name options with values are detected
     *
     * @expectedException \UnexpectedValueException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithValueSetWithEqualSignIsNotAllowed() {
        $argv = explode(" ", './test.php --help=8');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testLongNameWithValueSetWithEqualSignIsNotAllowed()

    /**
     * test that there is an exception when you try to set a value to short name options that does not take one
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameWithValueSetWithSpaceIsNotAllowed() {
        $argv = explode(" ", './test.php -h 8 -v');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameWithValueSetWithSpaceIsNotAllowed()
    
    /**
     * test that long name options with values are detected
     *
     * @expectedException \UnexpectedValueException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameWithValueSetWithSpaceIsNotAllowed() {
        $argv = explode(" ", './test.php --help 8 --verbose');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testLongNameWithValueSetWithSpaceIsNotAllowed()

    /**
     * test that there is an exception when you try to set an illegal short name
     *
     * @expectedException \InvalidArgumentException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testIllegalShortNameWithIsNotAllowed() {
        $argv = explode(" ", './test.php -w -h');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testIllegalShortNameWithIsNotAllowed()
    
    /**
     * test that there is an exception when you try to set an illegal long name
     *
     * @expectedException \InvalidArgumentException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testIllegalLongNameWithIsNotAllowed() {
        $argv = explode(" ", './test.php --work --verbose');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testIllegalLongNameWithIsNotAllowed()

    /**
     * test that there is an exception when you forget to set an value to a short name parameter that expects one
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameMissingExpectedValueAsMiddleArgument() {
        $argv = explode(" ", './test.php -c -v');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameMissingExpectedValueAsMiddleArgument()
    
    /**
     * test that there is an exception when you forget to set an value to a long name parameter that expects one
     *
     * @expectedException \UnexpectedValueException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameMissingExpectedValueAsMiddleArgument() {
        $argv = explode(" ", './test.php --count --verbose');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testLongNameMissingExpectedValueAsMiddleArgument()

    /**
     * test that there is an exception when you forget to set an value to a short name parameter that expects one
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameMissingExpectedValueAsLastArgument() {
        $argv = explode(" ", './test.php -v -c');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameMissingExpectedValueAsLastArgument()
    
    /**
     * test that there is an exception when you forget to set an value to a long name parameter that expects one
     *
     * @expectedException \UnexpectedValueException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameMissingExpectedValueAsLastArgument() {
        $argv = explode(" ", './test.php --verbose --count');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testLongNameMissingExpectedValueAsLastArgument()

    /**
     * test that there is an exception when you forget to set an value to a short name parameter that expects one
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameMissingExpectedValueAsSingleArgument() {
        $argv = explode(" ", './test.php -c');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameMissingExpectedValueAsSingleArgument()
    
    /**
     * test that there is an exception when you forget to set an value to a long name parameter that expects one
     *
     * @expectedException \UnexpectedValueException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameMissingExpectedValueAsSingleArgument() {
        $argv = explode(" ", './test.php --count');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testLongNameMissingExpectedValueAsSingleArgument()

    /**
     * test that there is an exception when you set multiple short name parameter that do not allow it
     *
     * @expectedException \UnexpectedValueException
     *
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testShortNameMultipleParameNotAllowed() {
        $argv = explode(" ", './test.php -h -h');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testShortNameMultipleParameNotAllowed()
    
    /**
     * test that there is an exception when you set multiple long name parameter that do not allow it
     *
     * @expectedException \UnexpectedValueException
     * 
     * @author Patrick Forget <patforg at webtrendi.com>
     */
    public function testLongNameMultipleParameNotAllowed() {
        $argv = explode(" ", './test.php --help --help');

        $argFilter = new TestCommandArgumentFilter($this->defaultDefinition, $argv);

        $argFilter->parseParams();

    } // testLongNameMultipleParameNotAllowed()

    
} // CommandArgumentFilterTest class
