<?php

namespace Fritsion;

class Language
{
    public static function load($lang = 'nl', $context = null)
    {
        $commonFile = __DIR__ . '/../lang/common/' . $lang . '.php';
        $common = file_exists($commonFile) ? include $commonFile : [];

        $specific = [];
        if ($context) {
            $specificFile = __DIR__ . "/../lang/{$context}/" . $lang . ".php";
            if (file_exists($specificFile)) {
                $specific = include $specificFile;
            }
        }

        return array_merge($common, $specific);
    }
}
