<?php namespace Jackiedo\LogReader\Contracts;

/**
 * Levelable
 *
 * @package Jackiedo\LogReader\Contracts
 * @author Jackie Do <anhvudo@gmail.com>
 * @copyright 2017
 * @access public
 */
class Patternable
{
    const HEADER_DATE        = '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}';
    const HEADER_ENVIRONMENT = '\w+';
    const HEADER_LEVEL       = '[A-Z]+';

    /**
     * Get pattern to for parsing logs header
     *
     * @return regex
     */
    public function getHeaderPattern()
    {
        return "/\[(" .self::HEADER_DATE. ")\]\s(" .self::HEADER_ENVIRONMENT. ")\.(" .self::HEADER_LEVEL. ")\:\s.*|\nNext\s.*/";
    }
}
