<?php

namespace Devinweb\TestParallel\Traits;

use Devinweb\TestParallel\ParallelTesting;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// use Illuminate\Testing\ParallelTesting;

trait TestDatabases
{
    /**
     * Indicates if the test database schema is up to date.
     *
     * @var bool
     */
    protected static $schemaIsUpToDate = false;

    /**
     * Boot a test database.
     *
     * @return void
     */
    protected function bootTestDatabase()
    {
        // (new ParallelTesting($this->app))->setUpProcess(function () {
        //     $this->whenNotUsingInMemoryDatabase(function ($database) {
        //         if ((new ParallelTesting($this->app))->option('recreate_databases')) {
        //             Schema::dropDatabaseIfExists(
        //                 $this->testDatabase($database)
        //             );
        //         }
        //     });
        // });

        (new ParallelTesting($this->app))->setUpTestCase(function ($testCase) {
            $uses = array_flip(class_uses_recursive(get_class($testCase)));

            $databaseTraits = [
                Testing\DatabaseMigrations::class,
                Testing\DatabaseTransactions::class,
                Testing\RefreshDatabase::class,
            ];

            if (Arr::hasAny($uses, $databaseTraits)) {
                $this->whenNotUsingInMemoryDatabase(function ($database) use ($uses) {
                    $testDatabase = $this->ensureTestDatabaseExists($database);

                    $this->switchToDatabase($testDatabase);

                    if (isset($uses[Testing\DatabaseTransactions::class])) {
                        $this->ensureSchemaIsUpToDate();
                    }
                });
            }
        });
    }

    /**
     * Ensure a test database exists and returns its name.
     *
     * @param string $database
     *
     * @return string
     */
    protected function ensureTestDatabaseExists($database)
    {
        return tap($this->testDatabase($database), function ($testDatabase) use ($database) {
            try {
                $this->usingDatabase($testDatabase, function () {
                    Schema::hasTable('dummy');
                });
            } catch (QueryException $e) {
                $this->usingDatabase($database, function () use ($testDatabase) {
                    Schema::dropDatabaseIfExists($testDatabase);
                    Schema::createDatabase($testDatabase);
                });
            }
        });
    }

    /**
     * Ensure the current database test schema is up to date.
     *
     * @return void
     */
    protected function ensureSchemaIsUpToDate()
    {
        if (! static::$schemaIsUpToDate) {
            Artisan::call('migrate');

            static::$schemaIsUpToDate = true;
        }
    }

    /**
     * Runs the given callable using the given database.
     *
     * @param string   $database
     * @param callable $callable
     *
     * @return void
     */
    protected function usingDatabase($database, $callable)
    {
        $original = DB::getConfig('database');

        try {
            $this->switchToDatabase($database);
            $callable();
        } finally {
            $this->switchToDatabase($original);
        }
    }

    /**
     * Apply the given callback when tests are not using in memory database.
     *
     * @param callable $callback
     *
     * @return void
     */
    protected function whenNotUsingInMemoryDatabase($callback)
    {
        $database = DB::getConfig('database');

        if ($database != ':memory:') {
            $callback($database);
        }
    }

    /**
     * Switch to the given database.
     *
     * @param string $database
     *
     * @return void
     */
    protected function switchToDatabase($database)
    {
        DB::purge();

        $default = config('database.default');

        $url = config("database.connections.{$default}.url");

        if ($url) {
            config()->set(
                "database.connections.{$default}.url",
                preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
            );
        } else {
            config()->set(
                "database.connections.{$default}.database",
                $database,
            );
        }
    }

    /**
     * Returns the test database name.
     *
     * @return string
     */
    protected function testDatabase($database)
    {
        $token = (new ParallelTesting($this->app))->token();

        return "{$database}_test_{$token}";
    }
}
