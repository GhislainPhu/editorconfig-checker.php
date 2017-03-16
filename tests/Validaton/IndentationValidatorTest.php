<?php

use PHPUnit\Framework\TestCase;
use EditorconfigChecker\Validation\IndentationValidator;
use EditorconfigChecker\Cli\Logger;

final class IndentationValidatorTest extends TestCase
{
    public function testValidateTabs()
    {
        $rules = ['indent_style' => 'tab'];
        $lineNumber = 1;
        $lastIndentSize = 1;
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $line = "\tHi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t\tHi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 2);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi\t";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "Hi\t";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t *Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = " *Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\t  Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "\t Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        $line = "    Hi\t";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);

        /* not the right relation (more than one indentation difference) */
        $line = "\t\t\tHi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 3);
        $this->assertEquals(Logger::getInstance()->countErrors(), 4);
    }

    public function testValidateSpaces()
    {
        $rules = ['indent_style' => 'space', 'indent_size' => 4];
        $lineNumber = 1;
        $lastIndentSize = 4;
        $file = 'src';

        /* clear the logger errors before */
        Logger::getInstance()->clearErrors();

        $line = 'Hi';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '        Hi';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 8);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = '    Hi    ';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 4);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = 'Hi    ';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 0);

        $line = "\tHi    ";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 0);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = " * Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 1);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "     * Hi";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 5);
        $this->assertEquals(Logger::getInstance()->countErrors(), 1);

        $line = "    \tHi    ";
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 4);
        $this->assertEquals(Logger::getInstance()->countErrors(), 2);

        /* not the right relation (more than one indentation difference) */
        $line = '            Hi';
        $this->assertEquals(IndentationValidator::validate($rules, $line, $lineNumber, $lastIndentSize, $file), 12);
        $this->assertEquals(Logger::getInstance()->countErrors(), 3);
    }
}
