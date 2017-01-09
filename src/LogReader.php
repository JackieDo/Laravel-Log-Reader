<?php namespace Jackiedo\LogReader;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Jackiedo\LogReader\Contracts\Levelable;
use Jackiedo\LogReader\Contracts\Patternable;
use Jackiedo\LogReader\Entities\LogEntry;
use Jackiedo\LogReader\Exceptions\UnableToRetrieveLogFilesException;

/**
 * LogReader
 *
 * @package Jackiedo\LogReader
 * @author Jackie Do <anhvudo@gmail.com>
 * @copyright 2017
 * @access public
 */
class LogReader
{
    /**
     * Store instance of Patternable for parsing log file
     * @var object
     */
    protected $patternable;

    /**
     * Store instance of Levelable to filter logs entry by level
     *
     * @var object
     */
    protected $levelable;

    /**
     * Stores the current environment to sort the log entries.
     *
     * @var string
     */
    protected $environment = null;

    /**
     * Stores the current level to sort the log entries.
     *
     * @var null|array
     */
    protected $level = null;

    /**
     * The log file path.
     *
     * @var string
     */
    protected $path = '';

    /**
     * Stores the filename to search log files for.
     *
     * @var string
     */
    protected $filename = '';

    /**
     * The current log file path.
     *
     * @var string
     */
    protected $currentLogPath = '';

    /**
     * Stores the field to order the log entries in.
     *
     * @var string
     */
    protected $orderByField = '';

    /**
     * Stores the direction to order the log entries in.
     *
     * @var string
     */
    protected $orderByDirection = '';

    /**
     * Stores the bool whether or
     * not to return read entries.
     *
     * @var bool
     */
    protected $includeRead = false;

    /**
     * Construct a new instance and set attributes.
     */
    public function __construct()
    {
        $this->patternable = new Patternable;
        $this->levelable = new Levelable;

        $this->setLogPath(Config::get('log-reader.path', storage_path('logs')));
        $this->setLogFilename(Config::get('log-reader.filename', 'laravel.log'));
        $this->setEnvironment(Config::get('log-reader.environment'));
        $this->setLevel(Config::get('log-reader.level'));
        $this->setOrderByField(Config::get('log-reader.order_by_field', ''));
        $this->setOrderByDirection(Config::get('log-reader.order_by_direction', ''));
    }

    /**
     * Sets the directory path to retrieve the
     * log files from.
     *
     * @param string $path
     */
    public function setLogPath($path)
    {
        $this->path = $path;
    }

    /**
     * Sets the log filename to retrieve the
     * logs data from.
     *
     * @param string $filename
     */
    public function setLogFilename($filename)
    {
        if (empty($filename)) {
            $this->filename = '*.*';
        } else {
            $this->filename = $filename;
        }
    }

    /**
     * Sets the currentLogPath property to
     * the specified path.
     *
     * @param string $path
     */
    private function setCurrentLogPath($path)
    {
        $this->currentLogPath = $path;
    }

    /**
     * Sets the orderByField property to the specified field.
     *
     * @param string $field
     */
    private function setOrderByField($field)
    {
        $field = strtolower($field);

        $fields = [
            'date',
            'level',
        ];

        if (in_array($field, $fields)) {
            $this->orderByField = $field;
        }
    }

    /**
     * Sets the orderByDirection property to the specified direction.
     *
     * @param string $direction
     */
    private function setOrderByDirection($direction)
    {
        $direction = strtolower($direction);

        if ($direction == 'desc' || $direction == 'asc') {
            $this->orderByDirection = $direction;
        }
    }

    /**
     * Sets the environment property to the specified environment.
     *
     * @param string $environment
     */
    private function setEnvironment($environment)
    {
        $environment = $environment;

        $this->environment = $environment;
    }

    /**
     * Sets the level property to the specified level.
     *
     * @param array $level
     */
    private function setLevel($level)
    {
        if (is_array($level)) {
            $this->level = $level;
        }
    }

    /**
     * Sets the includeRead property.
     *
     * @param bool $bool
     */
    private function setIncludeRead($bool = false)
    {
        $this->includeRead = $bool;
    }

    /**
     * Get instance of Patternable
     *
     * @return object Jackiedo\LogReader\Contracts\Patternable
     */
    public function getPatternable() {
        return $this->patternable;
    }

    /**
     * Get instance of Levelable
     * @return object Jackiedo\LogReader\Contracts\Levelable
     */
    public function getLevelable() {
        return $this->levelable;
    }

    /**
     * Retrieves the orderByField property.
     *
     * @return string
     */
    public function getOrderByField()
    {
        return $this->orderByField;
    }

    /**
     * Retrieves the orderByDirection property.
     *
     * @return string
     */
    public function getOrderByDirection()
    {
        return $this->orderByDirection;
    }

    /**
     * Retrieves the environment property.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Retrieves the level property.
     *
     * @return array
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Retrieves the currentLogPath property.
     *
     * @return string
     */
    public function getCurrentLogPath()
    {
        return $this->currentLogPath;
    }

    /**
     * Retrieves the path property.
     *
     * @return string
     */
    public function getLogPath()
    {
        return $this->path;
    }

    /**
     * Retrieves the log filename property.
     *
     * @return string
     */
    public function getLogFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the environment to sort the log entries by.
     *
     * @param string $environment
     *
     * @return $this
     */
    public function environment($environment)
    {
        $this->setEnvironment($environment);

        return $this;
    }

    /**
     * Sets the level to sort the log entries by.
     *
     * @param mixed $level
     *
     * @return $this
     */
    public function level($level)
    {
        if (empty($level)) {
            $level = [];
        } elseif (is_string($level)) {
            $level = explode(',', str_replace(' ', '', $level));
        } else {
            $level = is_array($level) ? $level : func_get_args();
        }

        $this->setLevel($level);

        return $this;
    }

    /**
     * Sets the filename to get log entries.
     *
     * @param string $filename
     *
     * @return $this
     */
    public function filename($filename)
    {
        $this->setLogFilename($filename);

        return $this;
    }

    /**
     * Includes read entries in the log results.
     *
     * @return $this
     */
    public function includeRead()
    {
        $this->setIncludeRead(true);

        return $this;
    }

    /**
     * Sets the direction to return the log entries in.
     *
     * @param string $field
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($field, $direction = 'asc')
    {
        $this->setOrderByField($field);
        $this->setOrderByDirection($direction);

        return $this;
    }

    /**
     * Returns a Laravel collection of log entries.
     *
     * @return Collection
     *
     * @throws UnableToRetrieveLogFilesException
     */
    public function get()
    {
        $entries = [];

        $files = $this->getLogFiles();

        if (is_array($files)) {
            /*
             * Retrieve the log files
             */
            foreach ($files as $log) {
                /*
                 * Set the current log path for easy manipulation
                 * of the file if needed
                 */
                $this->setCurrentLogPath($log['path']);

                /*
                 * Parse the log into an array of entries, passing in the level
                 * so it can be filtered
                 */
                $parsedLog = $this->parseLog($log['contents'], $this->getEnvironment(), $this->getLevel());

                /*
                 * Create a new LogEntry object for each parsed log entry
                 */
                foreach ($parsedLog as $entry) {
                    $newEntry = new LogEntry($entry);

                    /*
                     * Check if the entry has already been read,
                     * and if read entries should be included.
                     *
                     * If includeRead is false, and the entry is read,
                     * then continue processing.
                     */
                    if (!$this->includeRead && $newEntry->isRead()) {
                        continue;
                    }

                    $entries[] = $newEntry;
                }
            }

            /*
             * Return a new Collection of entries
             */
            return $this->postCollectionModifiers(new Collection($entries));
        }

        $message = 'Unable to retrieve files from path: '.$this->getLogPath();

        throw new UnableToRetrieveLogFilesException($message);
    }

    /**
     * Returns total of log entries.
     *
     * @return int
     */
    public function count() {
        return $this->get()->count();
    }

    /**
     * Finds a logged error by it's ID.
     *
     * @param string $id
     *
     * @return mixed|null
     */
    public function find($id = '')
    {
        $entries = $this->get()->filter(function ($entry) use ($id) {
            if ($entry->id === $id) {
                return true;
            }
        });

        return $entries->first();
    }

    /**
     * Marks all retrieved log entries as read and
     * returns the number of entries that have been marked.
     *
     * @return int
     */
    public function markRead()
    {
        $entries = $this->get();

        $count = 0;

        foreach ($entries as $entry) {
            if ($entry->markRead()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Deletes all retrieved log entries and returns
     * the number of entries that have been deleted.
     *
     * @return int
     */
    public function delete()
    {
        $entries = $this->get();

        $count = 0;

        foreach ($entries as $entry) {
            if ($entry->delete()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Deletes all retrieved log entries and returns
     * the number of entries that have been deleted.
     *
     * @return int
     */
    public function removeLogFile()
    {
        $files = $this->getLogFileList();

        $count = 0;

        foreach ($files as $file) {
            if (@unlink($file)) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Paginates the returned log entries.
     *
     * @param int $perPage
     *
     * @return mixed
     */
    public function paginate($perPage = 25)
    {
        $currentPage = $this->getPageFromInput();

        $offset = (($currentPage - 1) * $perPage);

        $entries = $this->get();

        $total = $entries->count();

        $entries = $entries->slice($offset, $perPage, true)->all();

        return new LengthAwarePaginator($entries, $total, $perPage);
    }

    /**
     * Modifies and returns the collection result if modifiers are set
     * such as an orderBy.
     *
     * @param Collection $collection
     *
     * @return Collection
     */
    private function postCollectionModifiers(Collection $collection)
    {
        if ($this->getOrderByField() && $this->getOrderByDirection()) {
            $collection = $this->processCollectionOrderBy($collection);
        }

        return $collection;
    }

    /**
     * Modifies the collection to be sorted by the orderByField and
     * orderByDirection properties.
     *
     * @param Collection $collection
     *
     * @return $this|Collection
     */
    private function processCollectionOrderBy(Collection $collection)
    {
        $field = $this->getOrderByField();

        $direction = $this->getOrderByDirection();

        $desc = false;

        if ($direction === 'desc') {
            $desc = true;
        }

        $sorted = $collection->sortBy(function ($entry) use ($field) {
            if (property_exists($entry, $field)) {
                return $entry->{$field};
            }
        }, SORT_NATURAL, $desc)->values()->all();

        return $sorted;
    }

    /**
     * Returns the current page from the current input.
     * Used for pagination.
     *
     * @return int
     */
    private function getPageFromInput()
    {
        $page = Input::get('page');

        if (is_numeric($page)) {
            return intval($page);
        }

        return 1;
    }

    /**
     * Parses the content of the file separating
     * the errors into a single array.
     *
     * @param string $content
     * @param string $allowedEnvironment
     * @param array $allowedLevel
     *
     * @return array
     */
    private function parseLog($content, $allowedEnvironment = null, $allowedLevel = [])
    {
        $log = [];

        // The regex pattern to match the log entry header
        $pattern = $this->patternable->getHeaderPattern();

        preg_match_all($pattern, $content, $headings);

        if (!is_array($headings)) return $log;
        list($headerList, $dateList, $envList, $levelList) = $headings;

        $stackList = preg_split($pattern, $content);

        if (trim($stackList[0]) == "") {
            array_shift($stackList);
        }

        $reFormated = false;
        $newContent = null;

        foreach ($headerList as $key => $header) {
            if (empty($dateList[$key])) {
                $dateList[$key]  = $dateList[$key-1];
                $envList[$key]   = $envList[$key-1];
                $levelList[$key] = $levelList[$key-1];
                $header          = str_replace("\nNext", "[".$dateList[$key]."] ".$envList[$key].".".$levelList[$key].":", $header);

                $reFormated = true;
            }

            $newContent .= $header.$stackList[$key];

            if ((empty($allowedEnvironment) || $allowedEnvironment == $envList[$key]) && $this->levelable->filter($levelList[$key], $allowedLevel)) {
                $log[] = [
                    'environment' => $envList[$key],
                    'level'       => $levelList[$key],
                    'date'        => $dateList[$key],
                    'header'      => $header,
                    'stack'       => $stackList[$key],
                    'filePath'    => $this->getCurrentLogPath(),
                ];
            }
        }

        if ($reFormated) {
            file_put_contents($this->getCurrentLogPath(), $newContent);
        }

        unset($pattern);
        unset($headings);
        unset($headerList);
        unset($dateList);
        unset($envList);
        unset($levelList);
        unset($reFormated);
        unset($newContent);

        return $log;
    }

    /**
     * Retrieves all the data inside each log file
     * from the log file list.
     *
     * @return array|bool
     */
    private function getLogFiles()
    {
        $data = [];

        $files = $this->getLogFileList();

        if (is_array($files)) {
            $count = 0;

            foreach ($files as $file) {
                $data[$count]['contents'] = file_get_contents($file);
                $data[$count]['path'] = $file;
                $count++;
            }

            return $data;
        }

        return false;
    }

    /**
     * Returns an array of log file paths.
     *
     * @param null|string $forceName
     *
     * @return bool|array
     */
    private function getLogFileList($forceName = null)
    {
        $path = $this->getLogPath();

        if (is_dir($path)) {

            /*
             * Matches files in the log directory with the special name'
             */
            $logPath = sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $this->getLogFilename());

            /*
             * Force matches all files in the log directory'
             */
            if (!is_null($forceName)) {
                $logPath = sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $forceName);
            }

            return glob($logPath, GLOB_BRACE);
        }

        return false;
    }

    /**
     * Returns an array of log filenames.
     *
     * @param null|string $filename
     *
     * @return array
     */
    public function getLogFilenameList($filename = null) {
        $data = [];
        if (empty($filename)) {
            $filename = '*.*';
        }
        $files = $this->getLogFileList($filename);

        if (is_array($files)) {
            foreach ($files as $file) {
                $basename = pathinfo($file, PATHINFO_BASENAME);
                $filename = pathinfo($file, PATHINFO_FILENAME);
                // $data[$basename] = preg_replace(['/\-\-*|\_\_*/', '/\s\s*/'], [' ', ' '], $filename);
                $data[$basename] = $basename;
            }
        }

        return $data;
    }
}
