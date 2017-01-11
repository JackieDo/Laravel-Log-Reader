<?php namespace Jackiedo\LogReader\Entities;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * LogEntry
 *
 * @package Jackiedo\LogReader\Entities
 * @author Jackie Do <anhvudo@gmail.com>
 * @copyright 2017
 * @access public
 */
class LogEntry
{
    /**
     * The entry's ID.
     *
     * @var string
     */
    public $id = '';

    /**
     * The entry's date string.
     *
     * @var string
     */
    public $date = '';

    /**
     * The entry's environment string.
     *
     * @var string
     */
    public $environment = '';

    /**
     * The entry's level string.
     *
     * @var string
     */
    public $level = '';

    /**
     * The entry's file path.
     *
     * @var string
     */
    public $filePath = '';

    /**
     * The entry's header string.
     *
     * @var string
     */
    public $header = '';

    /**
     * The entry's stack.
     *
     * @var array
     */
    public $stack = [];

    /**
     * The entry's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructs a new entry object with the specified attributes.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->setAttributes($attributes);

        $this->assignAttributes();
    }

    /**
     * Stores the entry in the cache so it is no longer shown
     * in the log results.
     *
     * @return mixed
     */
    public function markRead()
    {
        return Cache::rememberForever($this->makeCacheKey(), function () {
            return $this;
        });
    }

    /**
     * Returns true/false depending if the entry
     * has been marked read (exists inside the cache).
     *
     * @return bool
     */
    public function isRead()
    {
        if (Cache::has($this->makeCacheKey())) {
            return true;
        }

        return false;
    }

    /**
     * Removes the current entry from the log file.
     *
     * @return bool
     */
    public function delete()
    {
        $filePath = $this->getAttribute('filePath');

        $contents = file_get_contents($filePath);

        $contents = str_replace($this->getAttribute('header').$this->getAttribute('stack'), '', $contents);

        file_put_contents($filePath, $contents);

        return true;
    }

    /**
     * Retrieves an attribute by the specified key
     * from the attributes array.
     *
     * @param $key
     */
    public function getAttribute($key)
    {
        $attributes = $this->getAttributes();

        if (array_key_exists($key, $attributes)) {
            return $attributes[$key];
        }

        return;
    }

    /**
     * Alias of getAttribute() method.
     *
     * @param $key
     */
    public function getOriginal($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Returns a attributes array.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns a compressed entry header suitable to
     * be used as the entry's ID.
     *
     * @return string
     */
    private function makeId()
    {
        return md5($this->getAttribute('header'));
    }

    /**
     * Returns a key string for storing the entry
     * inside the cache.
     *
     * @return string
     */
    private function makeCacheKey()
    {
        return $this->id;
    }

    /**
     * Sets the entry's filePath property.
     *
     * @param $path
     */
    private function setFilePath($path = null)
    {
        if ($path) {
            $this->filePath = $path;
        }
    }

    /**
     * Sets the entry's level property.
     *
     * @param $header
     */
    private function setHeader($header = null)
    {
        if ($header) {
            $this->header = trim(str_replace("[".$this->getAttribute('date')."] ".$this->getAttribute('environment').".".$this->getAttribute('level').":", "", $header));
        }
    }

    /**
     * Sets the entry's date property.
     *
     * @param $date
     */
    private function setDate($date = null)
    {
        if ($date) {
            $this->date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }
    }

    /**
     * Sets the entry's level property.
     *
     * @param $stack
     */
    private function setStack($stack = null)
    {
        if ($stack) {
            $stackArray = preg_split('/\n?\#\d+\s/', trim(str_replace("Stack trace:\n", '', $stack)));
            array_shift($stackArray);

            $this->stack = $stackArray;
        }
    }

    /**
     * Sets the entry's environment property.
     *
     * @param $environment
     */
    private function setEnvironment($environment = null)
    {
        if ($environment) {
            $this->environment = strtolower($environment);
        }
    }

    /**
     * Sets the entry's level property.
     *
     * @param $level
     */
    private function setLevel($level = null)
    {
        if ($level) {
            $this->level = strtolower($level);
        }
    }

    /**
     * Sets the entry's ID property.
     *
     * @param $id
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Sets the attributes property.
     *
     * @param array $attributes
     */
    private function setAttributes($attributes = [])
    {
        if (is_array($attributes)) {
            $this->attributes = $attributes;
        }
    }

    /**
     * Assigns the valid keys in the attributes array
     * to the properties in the entry.
     */
    private function assignAttributes()
    {
        $this->setId($this->makeId());
        $this->setDate($this->getAttribute('date'));
        $this->setEnvironment($this->getAttribute('environment'));
        $this->setLevel($this->getAttribute('level'));
        $this->setFilePath($this->getAttribute('filePath'));
        $this->setHeader($this->getAttribute('header'));
        $this->setStack($this->getAttribute('stack'));
    }
}
