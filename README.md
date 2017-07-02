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
    - [Getting raw content of the log entry](#getting-raw-content-of-the-log-entry)
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
    - [Customize paginating with additional parameters](#customize-paginating-with-additional-parameters)
    - [Setting your own log parser](#setting-your-own-log-parser)
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

> **Note:** Instead of performing the above two steps, you can perform faster with the `$ composer require jackiedo/log-reader:2.*` command in your command line interface.

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

A Laravel collection is returned containing all of the log entries. Example:

    {
        26c90a5cdcc2d4c50b609d783b1b6355: {
            id           : "26c90a5cdcc2d4c50b609d783b1b6355",
            date         : ...,
            environment  : ...,
            level        : ...,
            file_path    : ...,
            context      : ...,
            stack_traces : ...
        },
        2627bda2c0f4ea7d79e2b15f28b73c36: {
            id           : "2627bda2c0f4ea7d79e2b15f28b73c36",
            date         : ...,
            environment  : ...,
            level        : ...,
            file_path    : ...,
            context      : ...,
            stack_traces : ...
        }
    }

This means you can use all Laravel handy collection functions, such as:

    LogReader::get()->first();
    LogReader::get()->filter($closure);
    LogReader::get()->pluck('header', 'id');
    LogReader::get()->search();
    // etc

Now you can loop over your results and display all the log entries. Example:

    $entries = LogReader::get();

    foreach ($entries as $entry) {
        returns $entry->context; // Returns the log entry context
    }

### The log entry's attributes
One log entry has the following attributes:

    /**
     * The Unique ID of the log entry.
     *
     * @var string
     */
    public $id;

    /**
     * The date of the log entry.
     *
     * @var \Carbon\Carbon
     */
    public $date;

    /**
     * The environment of the log entry.
     *
     * @var string
     */
    public $environment;

    /**
     * The level of the log entry.
     *
     * @var string
     */
    public $level;

    /**
     * The path to the log file containing the log entry.
     *
     * @var string
     */
    public $file_path;

    /**
     * The context of the log entry.
     *
     * @var \Jackiedo\LogReader\Entities\LogContext
     */
    public $context;

    /**
     * The stack trace entries of the log entry.
     * Each trace entry is an instance of
     * \Jackiedo\LogReader\Entities\TraceEntry
     *
     * @var \Illuminate\Support\Collection
     */
    public $stack_traces;

This is an example of the structure that you can obtain from the log entry:

    26c90a5cdcc2d4c50b609d783b1b6355: {
        id: "26c90a5cdcc2d4c50b609d783b1b6355",
        date: {
            date: "2017-06-29 10:18:32.000000",
            timezone_type: 3,
            timezone: "UTC"
        },
        environment: "local",
        level: "error",
        file_path: "D:\www\laravel-jackiedo54\storage\logs\laravel.log",
        context: {
            message: "md5() expects parameter 2 to be boolean, array given",
            exception: "ErrorException",
            in: "D:\www\laravel-jackiedo54\vendor\jackiedo\log-reader\src\Jackiedo\LogReader\Entities\LogEntry.php",
            line: "194"
        },
        stack_traces: [
            {
                caught_at: "Illuminate\Foundation\Bootstrap\HandleExceptions->handleError(2, 'md5() expects p...', 'D:\\www\\laravel-...', 194, Array)",
                in: "[internal function]",
                line: null
            },
            {
                caught_at: "md5('|', Array)",
                in: "D:\www\laravel-jackiedo54\vendor\jackiedo\log-reader\src\Jackiedo\LogReader\Entities\LogEntry.php",
                line: "194"
            },
            {
                caught_at: "Jackiedo\LogReader\Entities\LogEntry->generateId()",
                in: "D:\www\laravel-jackiedo54\vendor\jackiedo\log-reader\src\Jackiedo\LogReader\Entities\LogEntry.php",
                line: "352"
            },
            // etc
        ]
    },

You can access all attributes of the log entry through its property. Example:

    $logEntry->context->message;
    $logEntry->date->format('l jS \\of F Y h:i:s A');
    $logEntry->stack_traces->first()->caught_at;
    // etc

### Getting original attributes of the log entry
All attributes of a log entry are formatted informations through parsing from log file. If you want to get orginal attribute information, you can use the `getOriginal($attribute)` method. Example:

    $logEntry->getOriginal('context');      // Return original content string of context
    $logEntry->getOriginal('stack_traces'); // Return original content string of stack trace

### Getting raw content of the log entry
You can also get the raw content string of the log entry whenever you want.

    $logEntry->getRawContent();

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

    LogReader::find($id)->markAsRead();

This will cache this entry, and exclude it from any get log results in future.

### Marking all entries as read

    $marked = LogReader::markAsRead();

    return $marked;  // Returns the integer of how many entries were marked

This will cache all the entries and exclude them from future results.

### Including read entries in your request

    LogReader::withRead()->get();

    LogReader::withRead()->find($id);

    // etc.

### Deleting a log entry

    LogReader::find($id)->delete();

    // Or if you've marked this entry as read
    LogReader::withRead()->find($id)->delete();

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
You can easily order your results as well using the `orderBy($field[, $direction = 'asc'])` method:

    LogReader::orderBy('level')->get();
    LogReader::orderBy('date', 'desc')->get();

### Paginating result of your get log entries request

    LogReader::paginate(2);

This returns a regular Laravel pagination object. For example:

    {
        current_page: 1,
        data: {
            26c90a5cdcc2d4c50b609d783b1b6355: {...},
            2627bda2c0f4ea7d79e2b15f28b73c36: {...}
        },
        from: 1,
        last_page: 25,
        next_page_url: "/?page=2",
        path: "/",
        per_page: "2",
        prev_page_url: null,
        to: 2,
        total: 49
    }

You can use it how you'd typically use it on any eloquent model:

    /*
    |----------------------------------
    | In your controller
    |----------------------------------
    */

    $entries = LogReader::paginate(25);

    return view('logs', compact('entries'));

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

### Customize paginating with additional parameters
You already know how to simple paging with the `paginate()` method. Now, you should know that the `paginate()` method has total three parameters as follow:

    LogReader::paginate($perPage = 25, $currentPage = null, $options = []);

The second parameter is the page that you want to displayed. Pass the `null` value if you want to show page 1. Example:

    LogReader::paginate(10, 2);  // Display the second page

The third parameter is options that you want to set up for paginating, include base URL (path), page name (pageName), fragment (fragment) and query strings (query) that you want to append. Example:

    LogReader::paginate(10, null, [
        'path'     => 'your-path',
        'pageName' => 'display',
        'fragment' => 'your-anchor',
        'query'    => [
            'option1' => 'value1',
            'option2' => 'value2'
        ]
    ]);

These parameters are very useful for flexible paging. Take a look at the following example:

    ...
    public function index()
    {
        if ($this->request->has('current_page')) {
            return LogReader::paginate($this->request->has('per_page', 10), null, [
                'pageName' => 'current_page',
                'query' => [
                    'language' => 'english'
                ]
            ]);
        }

        return LogReader::get();
    }
    ...

### Setting your own log parser
Laravel Log Reader has a parser to use to analyze your log files. If you want to use your own parser, you need to follow these steps:

- First, the class of your log parser must implements the `Jackiedo\LogReader\Contracts\LogParser` interface:

```php
<?php namespace YourNamespace;

use Jackiedo\LogReader\Contracts\LogParser;

class YourOwnLogParser implements LogParser {
    //
}

```

- Next step, change the parser with the `setLogParser()` method:

```php
$parser = new \YourNamespace\YourOwnLogParser;
LogReader::setLogParser($parser)->get();
```

### Setting your own log path
By default, Laravel Log Reader uses the value of the `path` key in your the `log-reader.php` configuration file as the path to the directory contains your all log files. If you need this changed just set a different path using:

    LogReader::setLogPath('logs');

### Dependency injection
From now and then, it's possibly to use dependency injection to inject an instance of the LogReader class into your controller or other class. Example:

```php
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
```

### Exceptions
If you've set your log path manually and log files do not exist in the given directory, you will receive an `UnableToRetrieveLogFilesException` (full namespace is `Jackiedo\LogReader\Exceptions\UnableToRetrieveLogFilesException`).

## License
[MIT](LICENSE) © Jackie Do

## Thanks for use
Hopefully, this package is useful to you.