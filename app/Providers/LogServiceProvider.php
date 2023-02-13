<?php
namespace App\Providers;

use Illuminate\Log\LogServiceProvider as ServiceProvider;
use Illuminate\Log\Writer;
use \App;
use App\Log\LinnerWriter;
use Monolog\Logger as Monolog;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Configure the Monolog handlers for the application.
     *
     * @param Writer $log
     * @return void
     */
    protected function configureDailyHandler(Writer $log)
    {
        $prefix = App::runningInConsole() ? 'cli' : 'web';

        $log->useDailyFiles(
            $this->app->storagePath().'/logs/laravel-'. $prefix .'.log',
            $this->maxFiles(),
            $this->logLevel()
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param Writer $log
     * @return void
     */
    protected function configureSingleHandler(Writer $log)
    {
        $prefix = App::runningInConsole() ? 'cli' : 'web';

        $log->useFiles(
            $this->app->storagePath().'/logs/laravel-' . $prefix . '.log',
            $this->logLevel()
        );
    }

    /**
     * Create the logger.
     *
     * @return Writer
     */
    public function createLogger()
    {
        $log = new LinnerWriter(
            new Monolog($this->channel()),
            $this->app['events']
        );

        if ($this->app->hasMonologConfigurator()) {
            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandler($log);
        }

        return $log;
    }

}
