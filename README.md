# Laravel Log Reader
[![Latest Stable Version](https://poser.pugx.org/jackiedo/log-reader/v/stable)](https://packagist.org/packages/jackiedo/log-reader)
[![Total Downloads](https://poser.pugx.org/jackiedo/log-reader/downloads)](https://packagist.org/packages/jackiedo/log-reader)
[![Latest Unstable Version](https://poser.pugx.org/jackiedo/log-reader/v/unstable)](https://packagist.org/packages/jackiedo/log-reader)
[![License](https://poser.pugx.org/jackiedo/log-reader/license)](https://packagist.org/packages/jackiedo/log-reader)

Laravel Log Reader is an easy log reader and management tool for Laravel. You can easily view, manage, and modify the log entries. Using Laravel Log Reader is almost identical to using any Eloquent model.

# Overview
Look at one of the following topics to learn more about Laravel Log Reader

* [Versions and compatibility](#versions-and-compatibility)
* [Installation](#installation)
* [Usage](#usage)
    - [The LogReader facade](#the-logreader-facade)
    - [Getting all the log entries](#getting-all-the-log-entries)
    - [The log entry's attributes](#the-log-entrys-attributes)
    - [Getting original attributes of the log entry](#getting-original-attributes-of-the-log-entry)
    - [Counting total log entries](#counting-total-log-entries)
    - [Getting log entries from a special log file](#getting-log-entries-from-a-special-log-file)
    - [Getting all log filename list that you have](#getting-all-log-filename-list-that-you-have)
    - [Getting log entries by level](#getting-log-entries-by-level)
    - [Getting log entries by special environment](#getting-log-entries-by-special-environment)
    - [Finding a log entry](#finding-a-log-entry)
    - [Marking an entry as read](#marking-an-entry-as-read)
    - [Marking all entries as read](#marking-all-entries-as-read)
    - [Including read entries in your request](#including-read-entries-in-your-request)
    - [Deleting a log entry](#deleting-a-log-entry)
    - [Deleting all log entries](#deleting-all-log-entries)
    - [Removing the log file](#removing-the-log-file)
    - [Ordering result of your get log entries request](#ordering-result-of-your-get-log-entries-request)
    - [Paginating result of your get log entries request](#paginating-result-of-your-get-log-entries-request)
    - [Setting your own log path](#setting-your-own-log-path)
    - [Dependency injection](#dependency-injection)
    - [Exceptions](#exceptions)
* [License](#license)
* [Thanks from author](#thanks-for-use)

## Versions and compatibility
Currently, there are two branches of Laravel Log Reader is compatible with the following version of Laravel framework:

| Branch                                                                 | Laravel version  |
| ---------------------------------------------------------------------- | ---------------- |
| [1.x](https://github.com/JackieDo/Laravel-Log-Reader/tree/version-1.x) | 4.2              |
| [2.x](https://github.com/JackieDo/Laravel-Log-Reader/tree/version-2.x) | 5.*              |

This documentation is use for Laravel 5+. If you want to use on Laravel 4.2, please read at [here](https://github.com/JackieDo/Laravel-Log-Reader/tree/version-1.x)

## Installation
You can install this package through [Composer](https://getcomposer.org).

- First, edit your project's `composer.json` file to require `jackiedo/log-reader`:

```php
...
"require": {
    ...
    "jackiedo/log-reader": "2.*"
},
```

- Next, run the composer update command in your command line interface:

```shell
$ composer update
```

- Once update operation completes, the third step is add the service provider. Open `config/app.php`, and add a new line to the providers array:

```php
...
'providers' => array(
    ...
    Jackiedo\LogReader\LogReaderServiceProvider::class,
),
```

- The next step is add the follow line to the section `aliases` in file `config/app.php`:

```php
'LogReader' => Jackiedo\LogReader\Facades\LogReader::class,
```

> **Note:** Instead of performing the above two steps, you can perform faster with the `$ composer require jackiedo/log-reader:2.*` command in your command line interface.

- And the final step is publish configuration file:

```shell
$ php artisan vendor:publish --provider="Jackiedo\LogReader\LogReaderServiceProvider" --tag="config"
```

After that, you can set configuration for Laravel Log Reader with file `config/log-reader.php`

## Usage

### The LogReader facade
Laravel Log Reader has a facade with name is `Jackiedo\LogReader\Facades\LogReader`. You can do any operation with Log Reader through this facade.

### Getting all the log entries

    LogReader::get();

A Laravel collection is returned with all of the log entries. This means you can use all Laravels handy collection
functions, such as:

    LogReader::get()->first();
    LogReader::get()->filter($closure);
    LogReader::get()->lists('header', 'id');
    LogReader::get()->search();
    // etc

Now you can loop over your results and display all the log entries:

    $entries = LogReader::get();

    foreach ($entries as $entry) {
        returns $entry->header; // Returns the entry header
    }

### The log entry's attributes
One log entry has the following attributes:

    /**
     * Returns the entry date such as: 2015-03-19 14:56:08
     *
     * @var string Returns unique md5 string such as: fae8205b40bc9d6663db76011931716f
     */
    public $id;

    /**
     * Returns the level of the entry such as: emergency, alert, critical, error etc.
     *
     * @var string
     */
    public $level;

    /**
     * Returns the entry header string.
     *
     * @var string
     */
    public $header;

    /**
     * Returns the entry date, it's is an instance of Carbon\Carbon
     *
     * @var object Carbon\Carbon
     */
    public $date;

    /**
     * Returns array stacks trace of the error.
     *
     * @var array
     */
    public $stack;

    /**
     * Returns the complete file path of log file which contains the error.
     *
     * @var string
     */
    public $filePath;

### Getting original attributes of the log entry
All attributes of a log entry are reformatted informations through parsing from log file. If you want to get orginal attribute information, you can use the `getOriginal($attribute)` method. Example:

    $entries = LogReader::get();

    foreach ($entries as $entry) {
        $entry->getOriginal('stack'); // Return stack trace string
    }

### Counting total log entries

    LogReader::count();

### Getting log entries from a special log file
By default, Laravel Log Reader will read all log entries from special log files that you specified in configuration file. You can set filename of log files that you want to read from by:

    $log = LogReader::filename('laravel.log');
    $entries = $log->get();

    // Use with chaining method
    $entries = LogReader::filename('laravel.log')->get();

You can pass the filename parameter with compatible format string as in function `sprintf()`. Example:

    LogReader::filename('*.*')->get();            // Reade entries from all log files
    LogReader::filename('*.log')->get();          // Read entries from all files that has extension is .log
    LogReader::filename('monthly-*.log')->get();  // Read all files that filename started by 'monthly-' and has extension is .log
    // etc

Note: If you pass the filename parameter with `null` value, Laravel Log Reader will read all log files.

### Getting all log filename list that you have
Sometime, you want to get list of all your log files. This can be done easily through the `getLogFilenameList($filename)` method. Example:

    $files = LogReader::getLogFilenameList();
    $otherList = LogReader::getLogFilenameList('monthly-*.log');

### Getting log entries by level

    LogReader::level('error')->get();               // Only get error entries
    LogReader::level('error', 'debug')->get();      // Only get error and debug entries
    LogReader::level(['error', 'warning'])->get();  // Only get error and warning entries
    LogReader::level(null)->get();                  // Get all entries
    // etc

### Getting log entries by special environment

    LogReader::environment('local')->get();         // Only get entries for local environment
    LogReader::environment('production')->get();    // Only get entries for production environment
    LogReader::environment(null)->get();            // Get all entries for all environment
    // etc

### Finding a log entry

    LogReader::find($id);

### Marking an entry as read

    LogReader::find($id)->markRead();

This will cache this entry, and exclude it from any get log results in future.

### Marking all entries as read

    $marked = LogReader::markRead();

    return $marked;  // Returns the integer of how many entries were marked

This will cache all the entries and exclude them from future results.

### Including read entries in your request

    LogReader::includeRead()->get();

    LogReader::includeRead()->find($id);

    // etc.

### Deleting a log entry

    LogReader::find($id)->delete();

    // Or if you've marked this entry as read
    LogReader::includeRead()->find($id)->delete();

This will remove the entire entry from the log file, but keep all other entries in-tack.

### Deleting all log entries

    $deleted = LogReader::delete();

    return $deleted;  // Returns the integer of how many entries were deleted

    // Or delete entries in special log filename
    $deleted = LogReader::filename('special.log')->delete();

This will remove all entries in all log files. It will not delete the files however.

### Removing the log file

    // Remove special all log files
    $removed = LogReader::removeLogFile();
    return $removed; // Returns integer of how many file were deleted

    // Or remove special log file
    $removed = LogReader::filename('special.log')->removeLogFile();

This will delete log files. It also delete all entries in file, of course.

### Ordering result of your get log entries request
You can easily order your results as well using the `orderBy($field[, $direction = 'desc'])` method:

    LogReader::orderBy('level')->get();
    LogReader::orderBy('date', 'asc')->get();

### Paginating result of your get log entries request

    LogReader::paginate(25);

This returns a regular Laravel pagination object. You can use it how you'd typically use it on any eloquent model:

    /*
    |----------------------------------
    | In your controller
    |----------------------------------
    */

    $entries = LogReader::paginate(25);

    return View::make('logs', compact('entries'));

    /*
    |----------------------------------
    | In your view
    |----------------------------------
    */

    @foreach ($entries as $entry)
        {{ $entry->id }}
    @endforeach

    {{ $entries->links() }}

You can also combine functions with the pagination like so:

    $entries = LogReader::level('error')->paginate(25);

### Setting your own log path
By default, Laravel Log Reader uses the laravel helper `storage_path('logs')` as the log directory. If you need this changed just
set a different path using:

    LogReader::setLogPath('logs');

### Dependency injection
From now and then, it's possibly to use dependency injection to inject an instance of the LogReader class into your controller or other class. Example:

    <?php namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Http\Requests;
    use App\Product;
    use Illuminate\Http\Request;
    use Jackiedo\LogReader\LogReader;

    class TestLogReaderController extends Controller {

        protected $reader;

        public function __construct(LogReader $reader)
        {
            $this->reader = $reader;
        }

        public function index()
        {
            $reader = $this->reader->get();
        }
    }

### Exceptions
If you've set your log path manually and log files do not exist in the given directory, you will receive
an `UnableToRetrieveLogFilesException` (full namespace is `Jackiedo\LogReader\Exceptions\UnableToRetrieveLogFilesException`). For example:

    LogReader::setLogPath('testing')->get();  // Throws UnableToRetrieveLogFilesException

## License
[MIT](LICENSE) © Jackie Do

## Thanks for use
Hopefully, this package is useful to you.