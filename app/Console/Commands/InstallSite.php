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

        // build a connection to the wordpress database
        $wpConnection = DB::connection('wp_mysql');

        $domains = Domain::all();

        foreach ($domains as $domain) {
            // clear the site information string
            unset($siteInformation, $databaseName);

            // clear database username and password values
            $databaseName = $databaseUsername = $databasePassword = '';

            // build the site config files from the template files
            $phpConfigTemplateFile = $this->directories['templates'] . '/' . env('TEMPLATE_PHP');
            $phpConfigTemplate = fopen($phpConfigTemplateFile, 'r');

            // check for WP and pull the proper template
            if ($domain->is_word_press) {
                $nginxConfigTemplateFile = $this->directories['templates'] . '/' . env('TEMPLATE_WP');

                // generate the database username and password
                $databaseName = 'wp_' . substr(str_replace('.', '', $domain->name), 0, 6) . date('ynj');
                $databaseUsername = 'wpu_' . substr(str_replace('.', '', $domain->name), 0, 6) . date('ynj');
                $databasePassword = '' . str_random(24);

                // create the database
                $wpConnection->raw('CREATE DATABASE :databaseName', [
                    'databaseName' => $databaseName,
                ]);

                // create the database user
                $wpConnection->raw('GRANT ALL PRIVILEGES ON :databaseName TO :userName IDENTIFIED BY :userPass', [
                    'databaseName' => $databaseName . '.*',
                    'userName'     => $databaseUsername,
                    'userPass'     => $databasePassword,
                ]);
            } else {
                $nginxConfigTemplateFile = $this->directories['templates'] . '/' . env('TEMPLATE_SITE');
            }

            $nginxConfigTemplate = fopen($nginxConfigTemplateFile, 'r');

            $phpConfigContents = fread($phpConfigTemplate, filesize($phpConfigTemplateFile));
            $nginxConfigContents = fread($nginxConfigTemplate, filesize($nginxConfigTemplateFile));

            fclose($phpConfigTemplate);
            fclose($nginxConfigTemplate);

            // generate file names and write (then close) the files
            $phpConfigFileName = $this->directories['php'] . '/' . $domain->name . '.conf';
            $nginxConfigFileName = $this->directories['nginx'] . '/' . $domain->name;

            $phpConfig = fopen($phpConfigFileName, 'w');
            fwrite($phpConfig, $phpConfigContents);
            fclose($phpConfig);

            $nginxConfig = fopen($nginxConfigFileName, 'w');
            fwrite($nginxConfig, $nginxConfigContents);
            fclose($nginxConfig);

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