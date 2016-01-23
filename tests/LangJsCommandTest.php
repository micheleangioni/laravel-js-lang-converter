<?php

namespace MicheleAngioni\LaravelJsLangConverter;

use Illuminate\Filesystem\Filesystem as File;
use MicheleAngioni\LaravelJsLangConverter\Commands\LangJsCommand;
use MicheleAngioni\LaravelJsLangConverter\Generators\LangJsGenerator;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class LangJsCommandTest extends TestCase
{
    /**
     * Test the command.
     */
    public function testShouldCommandRun()
    {
        $generator = new LangJsGenerator(new File(), __DIR__.'/fixtures/lang');

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => __DIR__.'/output/lang.js']);

        $this->assertRunsWithSuccess($code);

        $this->assertFileExists(__DIR__.'/output/lang.js');

        $template = __DIR__.'/../src/MicheleAngioni/LaravelJsLangConverter/Generators/Templates/langjs_with_messages.js';

        $this->assertFileExists($template);

        $this->assertFileNotEquals($template, __DIR__.'/output/lang.js');
    }

    /**
     * @return void
     */
    public function testShouldTemplateHasHandlebars()
    {
        $template = __DIR__.'/../src/MicheleAngioni/LaravelJsLangConverter/Generators/Templates/langjs_with_messages.js';

        $this->assertFileExists($template);

        $contents = file_get_contents($template);

        $this->assertNotEmpty($contents);

        $this->assertHasHandlebars('messages', $contents);

        $this->assertHasHandlebars('langjs', $contents);
    }

    /**
     * @return void
     */
    public function testShouldOutputHasNotHandlebars()
    {
        $output = __DIR__.'/output/lang.js';

        $this->assertFileExists($output);

        $contents = file_get_contents($output);

        $this->assertNotEmpty($contents);

        $this->assertHasNotHandlebars('messages', $contents);

        $this->assertHasNotHandlebars('langjs', $contents);
    }

    /**
     * @return void
     */
    public function testAllFilesShouldbeConverted()
    {
        $generator = new LangJsGenerator(new File(), __DIR__.'/fixtures/lang');

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => __DIR__.'/output/lang.js']);

        $this->assertRunsWithSuccess($code);

        $output = __DIR__.'/output/lang.js';

        $this->assertFileExists($output);

        $contents = file_get_contents($output);

        $this->assertContains('createCongregation', $contents);
    }

    /**
     * @return void
     */
    public function testOnlySelectedFilesShouldbeConverted()
    {
        $this->app['config']->set('laravel_js_lang.files', [
            'validation'
        ]);

        $generator = new LangJsGenerator(new File(), __DIR__.'/fixtures/lang');

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => __DIR__.'/output/lang.js']);

        $this->assertRunsWithSuccess($code);

        $output = __DIR__.'/output/lang.js';

        $this->assertFileExists($output);

        $contents = file_get_contents($output);

        $this->assertNotContains('createCongregation', $contents);
    }

    /**
     * Run the command.
     * @param \Illuminate\Console\Command $command
     * @param array $input
     * @return int
     */
    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput());
    }

    /**
     * Assert the code return is success.
     * @param int  $code
     * @param null $message
     */
    protected function assertRunsWithSuccess($code, $message = null)
    {
        $this->assertEquals(0, $code, $message);
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasHandlebars($handle, $contents)
    {
        $this->assertEquals(1, preg_match('/\'\{(\s)'.preg_quote($handle).'(\s)\}\'/', $contents));
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasNotHandlebars($handle, $contents)
    {
        $this->assertEquals(0, preg_match('/\'\{(\s)'.preg_quote($handle).'(\s)\}\'/', $contents));
    }
}
