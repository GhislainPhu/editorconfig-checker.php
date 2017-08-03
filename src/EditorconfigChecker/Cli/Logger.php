<?php

namespace EditorconfigChecker\Cli;

class Logger
{
    /**
     * @var array
     */
    protected $errors = array();

    protected $fixed = false;

    protected $lines = 0;

    protected $files = 0;

    /**
     * @var Logger
     */
    protected static $instance = null;

    /**
     * Returns an instance of this class
     * If there is already an instance this would be returned
     * else wise a new one is created
     *
     * @return Logger
     */
    public static function getInstance() : Logger
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Protected constructor so no one else can access it
     */
    protected function __construct()
    {
    }

    /**
     * Adds an error to the logger
     *
     * @param string $message
     * @param string $file
     * @param int $lineNumber
     */
    public function addError($message, $fileName = null, $lineNumber = null)
    {
        $this->errors[] = ['lineNumber' => $lineNumber, 'fileName' => $fileName, 'message' => $message];
    }

    /**
     * Prints the errors from the logger to stdout
     */
    public function printErrors() : void
    {
        // only 1 error and no filename given = Fatal Error! (Eg. ".editorconfig not found!")
        if ($this->errors[0]['fileName'] === null && $this->countErrors() === 1) {
            printf('Fatal Error: %s' . PHP_EOL, $this->errors[0]['message']);
            return;
        }

        // sort error log by filename
        array_multisort(array_map(function ($element) {
            return $element['fileName'];
        }, $this->errors), SORT_ASC, $this->errors);

        $lastFile = '';
        $errorSegment = 0;
        foreach ($this->errors as $error) {
            if ($lastFile !== $error['fileName']) {
                $errorSegment++;
                $lastFile = $error['fileName'];
                printf("\033[33m%04d) %s\033[0m" . PHP_EOL, $errorSegment, $error['fileName']);
            }

            if (false === empty($error['lineNumber'])) {
                printf("      \033[31m%d\033[0m: ", $error['lineNumber']);
            } else {
                printf('      ');
            }
            printf('%s', $error['message']);
            printf(PHP_EOL);
        }

        printf(PHP_EOL);
        printf("\033[31m%d files checked, %d errors occurred\033[0m" .
            PHP_EOL, $this->getFiles(), $this->countErrors());
        printf('Check log above and fix the issues.' . PHP_EOL);

        if ($this->fixed) {
            printf(
                "\033[33mSome errors are automatically fixed, remember to add them to your git repository.\033[0m"
                . PHP_EOL
            );
        }
    }

    /**
     * Counts the errors which were added to the logger
     *
     * @return int
     */
    public function countErrors() : int
    {
        return count($this->errors);
    }

    /**
     * Clears all former added errors
     * This is just for the tests and should not be called
     * within the normal work flow
     *
     * @return void
     */
    public function clearErrors() : void
    {
        $this->errors = array();
    }

    public function errorFixed() : void
    {
        $this->fixed = true;
    }

    /**
     * This message is printed on the end if no error occurred
     *
     * @return void
     */
    public function printSuccessMessage() : void
    {
        printf("\033[32mSuccessfully checked %d lines in %d files :)\033[0m". PHP_EOL, $this->lines, $this->files);
    }

    /**
     * Set number of checked files
     *
     * @param int $files
     * @return void
     */
    public function setFiles(int $files) : void
    {
        $this->files = $files;
    }

    /**
     * Get the numer of checked files
     *
     * @return int
     */
    public function getFiles() : int
    {
        return $this->files;
    }

    /**
     * Adds lines to print them in success message
     *
     * @param int $lines
     * @return void
     */
    public function addLines(int $lines) : void
    {
        $this->lines += $lines;
    }
}
