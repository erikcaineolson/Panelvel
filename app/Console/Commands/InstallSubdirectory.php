<?php namespace App\Console\Commands;

use App\Helpers\WPComponents;
use App\Models\Domain;
use App\Models\Subdirectory;
use Illuminate\Console\Command;

class InstallSubdirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:subdirectory {subdirectoryId?} {--wordpress}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the subdirectory site with or without WordPress.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $subdirectoryId = $this->argument('subdirectoryId');
        $isWordPress = $this->option('wordpress');

        if (empty($subdirectoryId)) {
            $domains = Domain::onlyTrashed()->toArray();
            $domain = $this->choice('Which domain is the subdirectory under?', $domains);

            dd($domain);
        }

        $subdirectory = Subdirectory::find($subdirectoryId)->first();

        if ($isWordPress) {
            $fullPath = str_replace('//', '/', $subdirectory->domain->path . '/' . $subdirectory->moniker);
            $wpComponents = new WPComponents($subdirectory->domain->name, true);

            $bashCommand = storage_path() . '/bash/new_site.sh ' . strtolower($subdirectory->domain->name) . ' ' . $fullPath . ' wp_stub ' . $wpComponents->getDatabaseName() . ' ' . $wpComponents->getDatabasePassword() . ' ' . $wpComponents->getDatabaseUser() . ' ' . trim(trim($subdirectory->moniker), '/');

            $pythonCommand = storage_path() . '/python/create_db.py ' . $wpComponents->getDatabaseName() . ' ' . $wpComponents->getDatabaseUser() . ' ' . $wpComponents->getDatabasePassword() . ' ' . env('WP_DB_HOST');

            shell_exec($bashCommand);
            shell_exec($pythonCommand);
        } else {
            $directory = $subdirectory->domain->path . '/' . $subdirectory->moniker;
            $success = mkdir($directory, 0664, true);

            if ($success) {
                $this->output->success('Directory Created! Upload your site!');
            } else {
                $this->output->error('Something went wrong. Please try again.');
            }
        }
    }
}
