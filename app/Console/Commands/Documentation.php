<?php

namespace Yap\Console\Commands;

use Illuminate\Console\Command;
use Yap\Foundation\Documentation\Maintainer;

class Documentation extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yap:docs
                            {--i|install : Install documentation from repository}
                            {--u|update : Update newest documentation from repository (performs pull)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs documentation repository or pulls newest version of it.';

    /**
     * @var Maintainer
     */
    protected $maintainer;


    /**
     * Create a new command instance.
     *
     * @param Maintainer $maintainer
     */
    public function __construct(Maintainer $maintainer)
    {
        parent::__construct();
        $this->maintainer = $maintainer;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Using repository: '.config('documentation.git.repository').' with branch \''.config('documentation.git.branch').'\' and depth '.config('documentation.git.depth'),
            'vv');
        $action = $this->processOptions();

        if ($action === 'install') {
            $this->info('change directory to \''.config('documentation.path').'\'');
            $this->maintainer->install();
        } elseif ($action === 'update') {
            $this->maintainer->update();
        }
    }


    /**
     * @return string
     */
    private function processOptions(): string
    {
        if ($this->option('install') && $this->option('update')) {
            $action = $this->choice('You can either install or update, which is it?', ['install', 'update']);
        } elseif ( ! $this->option('install') && ! $this->option('update')) {
            $this->warn($this->getSynopsis());
            die();
        } elseif ($this->option('install')) {
            $action = 'install';
        } elseif ($this->option('update')) {
            $action = 'update';
        }

        if ($this->option('quiet')) {
            $this->maintainer->setQuiet(true);
        }

        return $action;
    }
}
