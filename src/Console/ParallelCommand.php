<?php

namespace Devinweb\TestParallel\Console;

// use Brotzka\DotenvEditor\DotenvEditor;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Process\Process;

class ParallelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:parallel
            {--bootstrap= : The bootstrap file to be used by PHPUnit}
            {--colors :  Displays a colored bar as a test result}
            {--c|--configuration= :  The PHPUnit configuration file to use}
            {--coverage-clover= :  Generate code coverage report in Clover XML format}
            {--coverage-cobertura= : Generate code coverage report in Cobertura XML format}
            {--coverage-crap4j= : Generate code coverage report in Crap4J XML format}
            {--coverage-html= : Generate code coverage report in HTML format}
            {--coverage-php= : Serialize PHP_CodeCoverage object to file}
            {--coverage-test-limit= : Limit the number of tests to record for each line of code. Helps to reduce memory and size of coverage reports}
            {--coverage-text : Generate code coverage report in text format}
            {--coverage-xml= : Generate code coverage report in PHPUnit XML format}
            {--exclude-group= :  Don\'t run tests from the specified group(s)}
            {--filter= : Filter (only for functional mode)}
            {--f|--functional : Run test methods instead of classes in separate processes}
            {--g|--group= : Only runs tests from the specified group(s)}
            {--log-junit= : Log test execution in JUnit XML format to file}
            {--log-teamcity= : Log test execution in Teamcity format to file}
            {--m|--max-batch-size= : Max batch size (only for functional mode)}
            {--no-coverage : Ignore code coverage configuration}
            {--no-test-tokens : Disable TEST_TOKEN environment variables. (default:  variable is set)}
            {--order-by= : Run tests in order:  default|random|reverse}
            {--parallel-suite : Run the suites of the config in parallel}
            {--passthru= : Pass the given arguments verbatim to the underlying test framework. Example:  --passthru="--prepend  xdebug-filter.php"}
            {--passthru-php= : Pass the given arguments verbatim to the underlying php process. Example:  --passthru-php="-d zend_extension=xdebug.so"}
            {--path= : An alias for the path argument}
            {--p|--processes= : The number of test processes to run}
            {--random-order-seed= : Use a specific random seed <N> for random order}
            {--runner= : Runner or WrapperRunner}
            {--stop-on-failure : Don\'t start any more processes after a failure}
            {--testsuite= : Filter which testsuite to run}
            {--tmp-dir= : Temporary directory for internal ParaTest files}
            {--whitelist=}
            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run parallel testing in PHPUnit';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $options = $this->options();

        $envs = App(Parser::class)->parseEnv($this->findPhpUnitFile());

        $process = new Process(
            array_merge(
                // Binary ...
                $this->binary(),
                // Arguments ...
                $this->paratestArguments($options)
            ),
            null,
            // Envs
            $envs
        );

        $process->setTimeout(null);

        return $process->run(function ($type, $line) {
            if (Process::ERR === $type) {
                $this->output->writeln("<bg=red;fg=white>$line</>");
            } else {
                $this->output->write($line);
            }
        });
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        $command = 'vendor/brianium/paratest/bin/paratest';
        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', $command];
        }

        return [PHP_BINARY, $command];
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param array $options
     *
     * @return array
     */
    protected function paratestArguments($options)
    {
        $options = Arr::where($options, function ($value, $key) {
            return is_string($value) || $value == true;
        });

        $arguments = $this->buildCommand($options);

        $file = $this->findPhpUnitFile();

        return array_merge([
            "--configuration=$file",
            "--runner=\Devinweb\TestParallel\ParallelRunner",
        ], $arguments);
    }

    /**
     * Generate the command options.
     *
     * @param array $options
     *
     * @return string
     */
    protected function buildCommand(array $options): array
    {
        $arguments = [];

        foreach ($options as $flag => $value) {
            if ($value === true) {
                array_push($arguments, "--{$flag}");
            } else {
                array_push($arguments, "--{$flag}=$value");
            }
        }

        return $arguments;
    }


    /**
     * Find the Phpunit path.
     *
     *
     * @return string
     */
    protected function findPhpUnitFile()
    {
        if (! file_exists($file = base_path('phpunit.xml'))) {
            $file = base_path('phpunit.xml.dist');
        }
        return $file;
    }
}
