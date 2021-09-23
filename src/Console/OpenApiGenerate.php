<?php

namespace Arkitechdev\OpenApi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Arkitechdev\OpenApi\Facades\OpenApi;
use Symfony\Component\Yaml\Yaml;

class OpenApiGenerate extends Command
{
    protected $signature = 'openapi:generate';

    protected $description = 'Generates the OpenAPI spec';

    public function handle()
    {
        if (!$this->configExists('openapi.php')) {
            if ($this->confirm('Configuration file does not exist. Create it?', false)) {
                $this->publishConfiguration();
                $this->info('Alright thats created.');
            } else {
                $this->info('Okay we\'ll use the defaults then.');
            }
        }

        $docsFile = config('openapi.docs_path');

        if (!is_file($docsFile)) {
            $this->error('The docs file does not exist. Start doc\'ing by creating a file in ' . $docsFile);
            return;
        }

        include_once $docsFile;

        $outputPath = config('openapi.output_path');

        $this->info('Generating...');

        if (strstr($outputPath, 'yaml') !== false) {
            $content = Yaml::dump(OpenApi::toArray(), 1024);
        } else {
            $content = OpenApi::toJson();
        }

        $this->info('Writing to ' . $outputPath);

        file_put_contents($outputPath, $content);

        $this->info('Finished!');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Arkitechdev\OpenApi\OpenApiServiceProvider",
            '--tag' => "config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
