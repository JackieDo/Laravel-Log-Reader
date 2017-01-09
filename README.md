[![Latest Stable Version](https://poser.pugx.org/jackiedo/log-reader/v/stable)](https://packagist.org/packages/jackiedo/log-reader)
[![Total Downloads](https://poser.pugx.org/jackiedo/log-reader/downloads)](https://packagist.org/packages/jackiedo/log-reader)
[![Latest Unstable Version](https://poser.pugx.org/jackiedo/log-reader/v/unstable)](https://packagist.org/packages/jackiedo/log-reader)
[![License](https://poser.pugx.org/jackiedo/log-reader/license)](https://packagist.org/packages/jackiedo/log-reader)

# Description

Laravel Log Reader is an easy log reader and management tool for Laravel. You're able to view, manage, and modify log entries
with ease. Using Laravel Log Reader is almost exactly like using any Eloquent model.

# Overview
Look at one of the following topics to learn more about Laravel Log Reader

* [Versions and compatibility](#versions-and-compatibility)
* [Installation](#installation)
* [Usage](#usage)
* [Exceptions](#exceptions)

## Versions and compatibility

Currently, there are some branches of Laravel Log Reader is compatible with the following version of Laravel framework

| Branch                                                                             | Laravel version  |
| ---------------------------------------------------------------------------------- | ---------------- |
| [version-1.x](https://github.com/JackieDo/Laravel-Log-Reader/tree/version-1.x)     | 4.2              |
| [version-2.x](https://github.com/JackieDo/Laravel-Log-Reader/tree/version-2.x)     | 5.3              |

This documentation is use for Laravel 4.2

## Installation

You can install this package through [Composer](https://getcomposer.org).

- First, edit your project's `composer.json` file to require `jackiedo/log-reader`:

```php
...
"require": {
    ...
    "jackiedo/log-reader": "1.*"
},
```

- Next, update Composer from the Terminal on your project source:

```shell
$ composer update
```

- Once update operation completes, the third step is add the service provider. Open `app/config/app.php`, and add a new item to the providers array:

```php
...
'providers' => array(
    ...
    'Jackiedo\LogReader\LogReaderServiceProvider',
),
```

- The next step is add the follow line to the section `aliases`:

```php
'LogReader' => 'Jackiedo\LogReader\Facades\LogReader',
```

- And the final step is publish configuration file:

```shell
$ php artisan config:publish jackiedo/log-reader
```

## Usage

#### Getting all the log entries, use:

    LogReader::get();

A laravel collection is returned with all of the entries. This means your able to use all of Laravels handy collection
functions such as:

    LogReader::get()->first();
    LogReader::get()->filter($closure);
    LogReader::get()->lists('header', 'id');
    LogReader::get()->search();
    // etc

Now you can loop over your results and display all the log entries:

    $entries = LogReader::get();

    foreach ($entries as $entry)
    {
        returns $entry->header; // Returns the entry header
    }

#### Log entry's attributes

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

Those attributes are reformatted information of log entries through parsing from log file. If you want to get orginal attribute information, you can use method `getOriginal($attribute)`. Example:

    $entry->getOriginal('stack'); // Return stack trace string

#### Counting total log entries

    LogReader::count();

#### Getting log entries from special log filename

By default, Laravel Log Reader will read all log entries from special log files that you specified in configuration file. You can set filename of log files that you want to read from by:

    LogReader::filename('laravel.log');
    $entries = LogReader::get();

    // Chaining example
    LogReader::filename('laravel.log')->get();

You can set filename with compatible format string in function `sprintf()` in PHP. Example:

    LogReader::filename('*.*')->get();              // Reade entries from all log files
    LogReader::filename('*.log')->get();            // Read entries from all files that has extension is .log
    LogReader::filename('monthly-*.log')->get();    // Read all files that filename started by 'monthly-' and has extension is .log
    // etc

Note: If you set filename is `null`, Laravel Log Reader will read all log files

#### Getting all log filename list that you have

Sometime, you want to have a list of your log files. This can be done easily through method `getLogFilenameList($filename = null)`. Example:

    $files = LogReader::getLogFilenameList();
    $otherList = LogReader::getLogFilenameList('monthly-*.log');

#### Getting log entries by level

    LogReader::level('error')->get();               // Only get error entries
    LogReader::level('error', 'debug')->get();      // Only get error and debug entries
    LogReader::level(['error', 'warning'])->get();  // Only get error and warning entries
    LogReader::level(null)->get();                  // Get all entries
    // etc

#### Getting log entries by special environment

    LogReader::environment('local')->get();         // Only get entries for local environment
    LogReader::environment('production')->get();    // Only get entries for production environment
    LogReader::environment(null)->get();            // Get all entries for all environment
    // etc

#### Finding a log entry:

    LogReader::find($id);

#### Marking an entry as read:

    LogReader::find($id)->markRead();

This will cache the entry, and exclude it from any future results.

#### Marking all entries as read:

    $marked = LogReader::markRead();

    return $marked; // Returns integer of how many entries were marked

This will cache all the entries and exclude them from future results.

#### Including read entries in your results:

    LogReader::includeRead()->get();

    LogReader::includeRead()->find($id);

    // etc.

#### Deleting a log entry:

    LogReader::find($id)->delete();

    // Or if you've marked the entry as read
    LogReader::includeRead()->find($id)->delete();

This will remove the entire entry from the log file, but keep all other entries in-tack.


#### Deleting all log entries:

    $deleted = LogReader::delete();

    return $deleted; // Returns integer of how many entries were deleted

    // Or delete entries in special log filename
    $deleted = LogReader::filename('special.log')->delete();

This will remove all entries in all log files. It will not delete the files however.

#### Remove all log file:

    $removed = LogReader::removeLogFile();

    return $removed; // Returns integer of how many file were deleted

    // Or remove special log filename
    $removed = LogReader::filename('special.log')->removeLogFile();

This will delete log files. It also delete all entries in file, of course.

#### Ordering

You can easily order your results as well using `orderBy($field, $direction = 'desc')`:

    LogReader::orderBy('level')->get();
    LogReader::orderBy('date', 'asc')->get();

#### Paginate your results

    LogReader::paginate(25);

This returns a regular Laravel pagination object. You can use it how you'd typically use it on any eloquent model:

    // In your controller

    $entries = LogReader::paginate(25);

    return View::make('logs', compact('entries'));

    // In your view

    @foreach ($entries as $entry)
        {{ $entry->id }}
    @endforeach

    {{ $entries->links() }}

You can also combine functions with the pagination like so:

    $entries = LogReader::level('error')->paginate(25);

#### Setting your own log path

By default LogReader uses the laravel helper `storage_path('logs')` as the log directory. If you need this changed just
set a different path using:

    LogReader::setLogPath('logs');

## Exceptions

#### UnableToRetrieveLogFilesException

If you've set your log path manually and log files do not exist in the given directory, you will receive
an `UnableToRetrieveLogFilesException` (full namespace is `Jackiedo\LogReader\Exceptions\UnableToRetrieveLogFilesException`).

For example:

    LogReader::setLogPath('testing')->get(); // Throws UnableToRetrieveLogFilesException