<?php

namespace EditorconfigChecker\Utilities;

class Utilities
{
    protected $defaults = [
        'vendor',
        'node_modules',
        '\.gif$',
        '\.png$',
        '\.bmp$',
        '\.jpg$',
        '\.svg$',
        '\.ico$',
        '\.lock$',
        '\.eot$',
        '\.woff$',
        '\.woff2$',
        '\.ttf$',
        '\.bak$',
        '\.bin$',
        '\.min.js$',
        '\.min.css$'
    ];

    /**
     * returns the end of line character from editorconfig rules
     *
     * @param array $rules
     * @returns string
     */
    public function getEndOfLineChar(array $rules) : string
    {
        if (isset($rules['end_of_line'])) {
            return
                ($rules['end_of_line'] == 'lf' ? "\n" :
                ($rules['end_of_line'] == 'cr' ? "\r" :
                ($rules['end_of_line'] == 'crlf' ? "\r\n" :
                '')));
        }

        return '';
    }

    /**
     * Saves a backup of the given file to /tmp
     *
     * @param string $filename
     * @return boolean
     */
    public function backupFile(string $filename) : bool
    {
        $tmpPath = '/tmp/editorconfig-checker.php/';
        if (is_file($filename) && (is_dir($tmpPath) || mkdir($tmpPath))) {
            return copy(
                $filename,
                $tmpPath . pathinfo($filename)['basename'] . '-' . time() . '-' . sha1_file($filename)
            );
        }

        return false;
    }

    /**
     * returns the default exclude pattern as array
     *
     * @return array
     */
    public function getDefaultExcludesAsArray() : array
    {
        return $this->defaults;
    }

    /**
     * returns the default exclude pattern as string
     *
     * @return string
     */
    public function getDefaultExcludesAsString() : string
    {
        return implode('|', $this->defaults);
    }
}
