<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use DB;

/**
 * Class InstallSite
 * @package App\Console\Commands
 */
class InstallSite extends Command
{
    /**
     * The name and signature of the console command
     *
     * @var string
     */
    protected $signature = 'install-site';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Runs specific scripts to install new websites';

    /**
     * The directories for everything we need to access
     *
     * @var array
     */
    protected $directories;

    protected function initCommand()
    {
        // normalize the directories:
        //  strip the last "/" in the event there is one, and add a new one,
        //  guaranteeing all directory structures are the same
        $this->directories = [
            'list'      => storage_path('sites/list.txt'),
            'nginx'     => storage_path('sites/nginx'),
            'php'       => storage_path('sites/php'),
            'templates' => storage_path('templates'),
        ];
    }

    /**
     * Execute the console command
     */
    public function handle()
    {
        $this->initCommand();

        // open the site list
        $siteList = fopen($this->directories['list'], 'w');

        $domains = Domain::all();

        foreach ($domains as $domain) {
            // clear the site information string
            unset($siteInformation, $databaseName);

            // clear database username and password values
            $databaseName = $databaseUsername = $databasePassword = '';

            // check for WP and pull the proper template
            if ($domain->is_word_press) {
                // generate the database username and password
                $databaseName = substr(str_replace('.', '', $domain->name), 0, 6) . date('ynj') . '_wp';
                $databaseUsername = substr(str_replace('.', '', $domain->name), 0, 6) . date('ynj') . '_wp';
                $databasePassword = '' . str_random(24);
            }

            // build the site information string
            //  format: domain name:username:password:is WP
            //  should be: domain.com:domauser:d0m4inP4$s!:true
            $siteInformation = '' . $domain->name . ':' . $domain->username . ':' . $domain->password . ':' . (int)$domain->is_word_press . ':' . $databaseName . ':' . $databaseUsername . ':' . $databasePassword . "\n";

            fwrite($siteList, $siteInformation);

            // soft-delete domain
            $domain->delete();
        }

        fclose($siteList);
    }
}