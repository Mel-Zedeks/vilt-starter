<?php

namespace MelZedeks\ViltStarter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vilt:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure your project to use Vue, Inertia, Laravel, and Tailwind css';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->installInertiaVueStack();
        return Command::SUCCESS;
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param string $after
     * @param string $name
     * @param string $group
     * @return void
     */
    protected function installMiddlewareAfter($after, $name, $group = 'web')
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (!Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = str_replace(
                $after . ',',
                $after . ',' . PHP_EOL . '            ' . $name . ',',
                $middlewareGroup,
            );

            file_put_contents(app_path('Http/Kernel.php'), str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $httpKernel
            ));
        }
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param mixed $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = "global";

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
            ,
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }


    /**
     * Update the "package.json" file.
     *
     * @param callable $callback
     * @param bool $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }


    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }


    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Get the path to the appropriate PHP binary.
     *
     * @return string
     */
    protected function phpBinary()
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }

    /**
     * Run the given commands.
     *
     * @param array $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> ' . $e->getMessage() . PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    ' . $line);
        });
    }

    /**
     * Remove Tailwind dark classes from the given files.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     * @return void
     */
    protected function removeDarkClasses(Finder $finder)
    {
        foreach ($finder as $file) {
            file_put_contents($file->getPathname(), preg_replace('/\sdark:[^\s"\']+/', '', $file->getContents()));
        }
    }

    /**
     * Install the Inertia Vue stack.
     *
     * @return void
     */
    protected function installInertiaVueStack()
    {
        // Install Inertia...
        $this->requireComposerPackages(
            'inertiajs/inertia-laravel:^0.6.3', 'laravel/sanctum:^2.8', 'tightenco/ziggy:^1.0',
            "spatie/laravel-medialibrary:^10.7",
            "spatie/laravel-permission:^5.7",
            "propaganistas/laravel-phone:^4.4",
        );

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    '@inertiajs/inertia' => '^0.11.0',
                    '@inertiajs/inertia-vue3' => '^0.6.0',
                    '@inertiajs/progress' => '^0.2.7',
                    '@tailwindcss/forms' => '^0.5.3',
                    '@vitejs/plugin-vue' => '^4.0.0',
                    'autoprefixer' => '^10.4.12',
                    'postcss' => '^8.4.18',
                    'tailwindcss' => '^3.2.1',
                    'vue' => '^3.2.41',
                ] + $packages;
        });

        $this->updateNodePackages(function ($packages) {
            return [
                    "@fortawesome/fontawesome-svg-core" => "^6.2.1",
                    "@fortawesome/free-brands-svg-icons" => "^6.2.1",
                    "@fortawesome/free-regular-svg-icons" => "^6.2.1",
                    "@fortawesome/free-solid-svg-icons" => "^6.2.1",
                    "@fortawesome/vue-fontawesome" => "^3.0.3",
                    "@tailwindcss/line-clamp" => "^0.4.2",
                    "moment" => "^2.29.4",
                    "pinia" => "^2.0.28",
                    "vue-multiselect" => "^3.0.0-alpha.2",
                    "zedeks-vue-inertia-datatable" => "^2.1.0"
                ] + $packages;
        }, false);


        (new Filesystem)->ensureDirectoryExists(app_path('Filters'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Filters', app_path('Filters'));

        (new Filesystem)->ensureDirectoryExists(app_path('Notifications'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Notifications', app_path('Notifications'));

        (new Filesystem)->ensureDirectoryExists(app_path('Services'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Services', app_path('Services'));

        (new Filesystem)->ensureDirectoryExists(app_path('Traits/'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Traits', app_path('Traits'));

        (new Filesystem)->ensureDirectoryExists(app_path('Broadcasting/'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Broadcasting', app_path('Broadcasting'));

        (new Filesystem)->ensureDirectoryExists(app_path('Console/Commands'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Console/Commands', app_path('Console/Commands'));

        (new Filesystem)->ensureDirectoryExists(resource_path('js/Composable'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Composable', resource_path('js/Composable'));

        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/Components', resource_path('js/Components'));

        (new Filesystem)->ensureDirectoryExists(resource_path('views/mails'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/views/mails', resource_path('views/mails'));


        // Requests...
//        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
//        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Requests', app_path('Http/Requests'));

        // Middleware...
        $this->installMiddlewareAfter('SubstituteBindings::class', '\App\Http\Middleware\HandleInertiaRequests::class');
        $this->installMiddlewareAfter('\App\Http\Middleware\HandleInertiaRequests::class', '\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class');

        copy(__DIR__ . '/../../stubs/Middleware/HandleInertiaRequests.php', app_path('Http/Middleware/HandleInertiaRequests.php'));

        // Views...
        copy(__DIR__ . '/../../stubs/views/app.blade.php', resource_path('views/app.blade.php'));

        copy(__DIR__ . '/../../stubs/config/smsgist.php', base_path('config/smsgist.php'));

//        copy(__DIR__ . '/../../stubs/Services/PageTitle.php', app_path('Services/PageTitle.php'));
//        copy(__DIR__ . '/../../stubs/Services/SMSGist.php', app_path('Services/SMSGist.php'));


        if (!(new Filesystem)->exists(resource_path('js/Pages'))) {
            (new Filesystem)->makeDirectory(resource_path('js/Pages'));
        }
//        if (!(new Filesystem)->exists(resource_path('js/Components'))) {
//            (new Filesystem)->makeDirectory(resource_path('js/Components'));
//        }

        if (!(new Filesystem)->exists(resource_path('js/Layouts'))) {
            (new Filesystem)->makeDirectory(resource_path('js/Layouts'));
        }
        if (!(new Filesystem)->exists(resource_path('js/store'))) {
            (new Filesystem)->makeDirectory(resource_path('js/store'));
        }


//        if (!$this->option('dark')) {
//            $this->removeDarkClasses((new Finder)
//                ->in(resource_path('js'))
//                ->name('*.vue')
//                ->notName('Welcome.vue')
//            );
//        }

        // "Dashboard" Route...
//        $this->replaceInFile('/home', '/dashboard', resource_path('js/Pages/Welcome.vue'));
//        $this->replaceInFile('Home', 'Dashboard', resource_path('js/Pages/Welcome.vue'));
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Tailwind / Vite...
        copy(__DIR__ . '/../../stubs/css/app.css', resource_path('css/app.css'));
        copy(__DIR__ . '/../../stubs/css/multiselect.css', resource_path('css/multiselect.css'));
        copy(__DIR__ . '/../../stubs/css/zdatatable.css', resource_path('css/zdatatable.css'));
        copy(__DIR__ . '/../../stubs/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__ . '/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../stubs/jsconfig.json', base_path('jsconfig.json'));
        copy(__DIR__ . '/../../stubs/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__ . '/../../stubs/js/app.js', resource_path('js/app.js'));


        $this->runCommands(['npm install', 'npm run build']);

        $this->line('');
        $this->components->info('Vilt Starter scaffolding installed successfully.');
    }
}
