<?php

namespace Simplon\Mustache;

/**
 * Mustache
 * @package Simplon\Mustache
 * @author Tino Ehrich (tino@bigpun.me)
 */
class Mustache
{
    /**
     * @var array
     */
    private static $data;

    /**
     * @var array
     */
    private static $templates = [];

    /**
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    public static function render($template, array $data)
    {
        // cache data
        self::$data = $data;

        // parse template
        $template = self::parse($template, $data);

        // clean left overs and reset data
        return self::finaliseTemplate($template);
    }

    /**
     * @param $pathTemplate
     * @param array $data
     *
     * @return string
     * @throws MustacheException
     */
    public static function renderByFile($pathTemplate, array $data)
    {
        if (isset(self::$templates[$pathTemplate]) === false)
        {
            // make sure the file exists
            if (file_exists($pathTemplate) === false)
            {
                throw new MustacheException('Missing given template file: ' . $pathTemplate);
            }

            // fetch template
            $template = file_get_contents($pathTemplate);

            if ($template === false)
            {
                throw new MustacheException('Could not load template file: ' . $pathTemplate);
            }

            // cache template
            self::$templates[$pathTemplate] = $template;
        }

        return self::render(self::$templates[$pathTemplate], $data);
    }

    /**
     * @param $template
     * @param array $data
     *
     * @return string
     */
    private static function parse($template, array $data)
    {
        foreach ($data as $key => $val)
        {
            if (is_array($val) && empty($val) === false)
            {
                // find loops
                preg_match_all('|{{#' . $key . '}}(.*?){{/' . $key . '}}|sm', $template, $foreachPattern);

                // handle loops
                if (isset($foreachPattern[1][0]))
                {
                    foreach ($foreachPattern[1] as $patternId => $patternContext)
                    {
                        $loopContent = '';

                        // handle array objects
                        if (isset($val[0]))
                        {
                            foreach ($val as $loopVal)
                            {
                                $loopContent .= self::parse($patternContext, $loopVal);
                            }
                        }

                        // normal array only
                        else
                        {
                            $loopContent = self::parse($patternContext, $val);
                        }

                        // replace pattern context
                        $template = preg_replace(
                            '|' . preg_quote($foreachPattern[0][$patternId]) . '|s',
                            $loopContent,
                            $template,
                            1
                        );
                    }
                }
            }

            // ----------------------------------

            elseif (is_bool($val) || empty($val) === true)
            {
                // determine true/false
                $conditionChar = $val === true ? '\#' : '\^';

                // find bools
                preg_match_all('|{{' . $conditionChar . $key . '}}(.*?){{/' . $key . '}}|s', $template, $boolPattern);

                // handle bools
                if (isset($boolPattern[1][0]))
                {
                    foreach ($boolPattern[1] as $patternId => $patternContext)
                    {
                        // parse and replace pattern context
                        $template = preg_replace(
                            '|' . preg_quote($boolPattern[0][$patternId]) . '|s',
                            self::parse($patternContext, self::$data),
                            $template,
                            1
                        );
                    }
                }
            }

            // ----------------------------------

            elseif ($val instanceof \Closure)
            {
                // set closure return
                $template = str_replace('{{' . $key . '}}', $val(), $template);
            }

            // ----------------------------------

            else
            {
                // set var: unescaped
                $template = str_replace('{{{' . $key . '}}}', $val, $template);

                // set var: escaped
                $template = str_replace('{{' . $key . '}}', htmlspecialchars($val), $template);
            }
        }

        return (string)$template;
    }

    /**
     * @param string $template
     *
     * @return string
     */
    private static function finaliseTemplate($template)
    {
        // remove left over wrappers
        $template = preg_replace('|{{.*?}}.*?{{/.*?}}\n*|s', '', $template);

        // remove left over variables
        $template = preg_replace('|{{.*?}}\n*|s', '', $template);

        return (string)$template;
    }
}