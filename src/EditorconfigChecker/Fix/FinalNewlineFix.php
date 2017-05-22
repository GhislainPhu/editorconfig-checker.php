<?php

namespace EditorconfigChecker\Fix;

use EditorconfigChecker\Cli\Logger;

class FinalNewlineFix
{
    /**
     * Insert a final newline at the end of the file
     *
     * @param string $file
     * @return boolean
     */
    public static function insert($filename)
    {
        if (is_file($filename)) {
            return file_put_contents($filename, PHP_EOL, FILE_APPEND);
        }

        return false;
    }

    /**
     * Removes a final newline at the end of the file
     *
     * @param string $file
     * @return boolean
     */
    public static function remove($file)
    {
    }
}
