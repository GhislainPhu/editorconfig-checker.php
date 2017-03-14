<?php

use PHPUnit\Framework\TestCase;
use MStruebing\EditorconfigChecker\Validation\TrailingWhitespaceValidator;
use MStruebing\EditorconfigChecker\Cli\Logger;

final class TrailingWhitespaceValidatorTest extends TestCase
{
    public function testValidate()
    {
        $rules = ['trim_trailing_whitespace' => true];
        $lineNumber = 1;
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $line = 'heyho';
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '';
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = ' heyho';
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\theyho";
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = 'heyho ';
        $this->assertFalse(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "heyho\t";
        $this->assertFalse(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = ' ';
        $this->assertFalse(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        /* trim_trailing_whitespace have to be true explicitly */
        $line = 'ww ';
        $rules = ['trim_trailing_whitespace'];
        $this->assertTrue(TrailingWhitespaceValidator::validate($rules, $line, $lineNumber, $file));
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }
}
