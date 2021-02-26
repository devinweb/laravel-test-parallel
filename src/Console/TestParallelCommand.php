<?php

namespace Devinweb\TestParallel\Console;

use Illuminate\Console\Command;

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
        $options = $this->buildCommand($this->options());
        exec("vendor/bin/paratest {$options}", $output);
        $this->output($output);
    }


    /**
     * Style the output that should be displayed to the end user
     *
     * @param array $output
     *
     * @return void
     */
    protected function output(array $output)
    {
        foreach ($output as $value) {
            echo $value . "\n";
        }
    }

    /**
     * Generate the command options
     *
     * @param array $options
     *
     * @return string
     */
    protected function buildCommand(array $options): string
    {
        $option = '';

        foreach ($options as $flag => $value) {
            if ($value) {
                $option .=" --{$flag}=$value";
            }
        }

        return $option;
    }
}
