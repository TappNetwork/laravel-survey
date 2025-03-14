<?php

namespace MattDaneshvar\Survey;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use MattDaneshvar\Survey\Http\View\Composers\SurveyComposer;

class SurveyServiceProvider extends ServiceProvider
{
    /**
     * Boot the package.
     *
     * @param  ViewFactory  $viewFactory
     */
    public function boot(ViewFactory $viewFactory)
    {
        $this->publishes([
            __DIR__.'/../config/survey.php' => config_path('survey.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views/' => base_path('resources/views/vendor/survey'),
        ], 'views');

        $this->mergeConfigFrom(__DIR__.'/../config/survey.php', 'survey');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'survey');

        $viewFactory->composer('survey::standard', SurveyComposer::class);

        $this->publishMigrations([
            '100000_create_surveys_table',
            '200000_create_sections_table',
            '300000_create_questions_table',
            '400000_create_entries_table',
            '500000_create_answers_table',
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Answer::class, \MattDaneshvar\Survey\Models\Answer::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Entry::class, \MattDaneshvar\Survey\Models\Entry::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Question::class, \MattDaneshvar\Survey\Models\Question::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Section::class, \MattDaneshvar\Survey\Models\Section::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Survey::class, \MattDaneshvar\Survey\Models\Survey::class);
    }

    /**
     * Publish package migrations.
     *
     * @param $migrations
     */
    protected function publishMigrations($migrations)
    {
        foreach ($migrations as $migration) {
            $migrationClass = Str::studly($migration);

            if (class_exists($migrationClass)) {
                return;
            }

            $this->publishes([
                __DIR__."/../database/migrations/$migration.php.stub" => database_path('migrations/'.date('Y_m_d',
                        time())."_$migration.php"),
            ], 'migrations');
        }
    }
}
