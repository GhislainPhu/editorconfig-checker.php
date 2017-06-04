<?php
namespace EditorconfigChecker\Editorconfig;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Editorconfig
{
    /**
     * Returns the editorconfig as an array
     *
     * @param string $editorconfigPath
     * @return array
     */
    public function getRulesAsArray($editorconfigPath)
    {
        return parse_ini_file($editorconfigPath, true);
    }

    protected function getNearestEditorconfigRules($baseDir)
    {
        $baseEditorconfig = $baseDir . '/.editorconfig';
        if (is_file($baseEditorconfig)) {
            return $this->getRulesAsArray($baseEditorconfig);
        } else {
            return $this->getNearestEditorconfigRules(dirname($baseDir));
        }
    }

    /**
     * Returns the editorconfig rules for a file
     *
     * @param string $fileName
     * @return array
     */
    public function getRulesForFile($fileName)
    {
        $editorconfig = $this->getNearestEditorconfigRules(getcwd() . pathinfo(substr($fileName, 1))['dirname']);

        return array_reduce(array_keys($editorconfig), function ($carry, $pattern) use ($editorconfig, $fileName) {
            $rules = $editorconfig[$pattern];

            return Glob::match(sprintf('%s/%s', getcwd(), $fileName), Path::makeAbsolute('**/' . $pattern, getcwd())) ?
                array_merge($carry, $rules) : $carry;
        }, []);
    }
}
