<?php namespace MicheleAngioni\LaravelJsLangConverter\Generators;

use Illuminate\Filesystem\Filesystem as File;
use JShrink\Minifier;

class LangJsGenerator
{
    /**
     * The file service.
     */
    protected $file;

    /**
     * The source path of the language files.
     */
    protected $sourcePath;

    /**
     * Construct a new LangJsGenerator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $file
     * @param  string  $sourcePath
     */
    public function __construct(File $file, $sourcePath)
    {
        $this->file = $file;
        $this->sourcePath = $sourcePath;
    }

    /**
     * Generate a JS lang file from all language files to the $target file.
     *
     * @param string  $target
     * @param array  $options
     *
     * @return int
     */
    public function generate($target, $options)
    {
        $messages = $this->getMessages();
        $this->prepareTarget($target);

        $template = $this->file->get(__DIR__ . '/Templates/langjs_with_messages.js');
        $langjs = $this->file->get(__DIR__ . '/../../../js/lang.js');

        $template = str_replace('\'{ messages }\'', json_encode($messages), $template);
        $template = str_replace('\'{ langjs }\';', $langjs, $template);

        if ($options['compress']) {
            $template = Minifier::minify($template);
        }

        return $this->file->put($target, $template);
    }

    /**
     * Return all language messages.
     *
     * @throws \Exception
     * @return array
     */
    protected function getMessages()
    {
        $messages = [];
        $sourcePath = $this->sourcePath;

        if ( ! $this->file->exists($sourcePath)) {
            throw new \Exception("${sourcePath} doesn't exists!");
        }

        foreach ($this->file->allFiles($sourcePath) as $file) {
            $pathName = $file->getRelativePathName();

            if ( $this->file->extension($pathName) !== 'php' ) {
                continue;
            }

            if(!$this->fileIsIncludedInFileList(substr($file->getFileName(), 0, -4))) {
                continue;
            }

            $key = substr($pathName, 0, -4);
            $key = str_replace('\\', '.', $key);
            $key = str_replace('/', '.', $key);

            $messages[ $key ] = include "${sourcePath}/${pathName}";
        }

        return $messages;
    }

    /**
     * Check if input file is included in the file list to be converted.
     * If the file list is empty, all files will be included.
     *
     * @param  string  $fileName
     * @return bool
     */
    protected function fileIsIncludedInFileList($fileName)
    {
        if(!config('laravel_js_lang.files') || count(config('laravel_js_lang.files')) === 0) {
            return true;
        }

        if(in_array($fileName, config('laravel_js_lang.files'))) {
            return true;
        }

        return false;
    }

    /**
     * Prepare the target directory.
     *
     * @param  string  $target
     */
    protected function prepareTarget($target)
    {
        $dirname = dirname($target);

        if ( ! $this->file->exists($dirname) ) {
            $this->file->makeDirectory($dirname);
        }
    }
}
