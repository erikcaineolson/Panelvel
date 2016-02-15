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

    /**
     * Is this a WP site?
     *
     * @var bool
     */
    protected $isWordPress;

    /**
     * Holds the contents of the NGINX config file
     *
     * @var string
     */
    protected $nginxConfigFile;

    /**
     * Holds the contents of the PHP config file
     *
     * @var string
     */
    protected $phpConfigFile;

    /**
     * Contains the search values to replace with
     *
     * @var array
     */
    protected $replaceReplaceValues;

    /**
     * Contains the search values to replace
     *
     * @var array
     */
    protected $replaceSearchValues;

    /**
     * The authorization keys retrieved via WP API
     *
     * @var array
     */
    protected $wpNonces;

    /**
     * Set NGINX config file
     */
    protected function setNginxConfigFile()
    {
        $nginxConfigFilename = $this->isWordPress ? env('TEMPLATE_SITE') : env('TEMPLATE_WP');
        $this->nginxConfigFile = file_get_contents($this->directories['templates'] . '/' . $nginxConfigFilename);
    }

    /**
     * Set PHP config file
     */
    protected function setPhpConfigFile()
    {
        $phpConfigFilename = env('TEMPLATE_PHP');
        $this->phpConfigFile = file_get_contents($this->directories['php'] . '/' . $phpConfigFilename);
    }

    /**
     * Set replacement values
     */
    protected function setReplaceReplaceValues($domainName)
    {
        if($this->isWordPress){
            $this->setWpNonces();

            $this->replaceSearchValues = [
                $domainName,
                'wp_' . substr(str_replace('.', '', $domainName), 6) . date('ymd') . '_wp',
                'wp_' . substr(str_replace('.', '', $domainName), 6) . date('ymd') . '_u',
                '' . string_random(24),
                env('WP_DB_HOSTNAME'),
                $this->wpNonces[0],
                $this->wpNonces[1],
                $this->wpNonces[2],
                $this->wpNonces[3],
                $this->wpNonces[4],
                $this->wpNonces[5],
                $this->wpNonces[6],
                $this->wpNonces[7],
            ];
        }else{
            $this->replaceReplaceValues = [$domainName];
        }
    }

    /**
     * Set search values
     */
    protected function setReplaceSearchValues()
    {
        if($this->isWordPress){
            $this->replaceSearchValues = [
                'SITE_NAME',
                'database_name_here',
                'username_here',
                'password_here',
                'localhost',
                'define(\'AUTH_KEY\',         \'put your unique phrase here\')',
                'define(\'SECURE_AUTH_KEY\',  \'put your unique phrase here\')',
                'define(\'LOGGED_IN_KEY\',    \'put your unique phrase here\')',
                'define(\'NONCE_KEY\',        \'put your unique phrase here\')',
                'define(\'AUTH_SALT\',        \'put your unique phrase here\')',
                'define(\'SECURE_AUTH_SALT\', \'put your unique phrase here\')',
                'define(\'LOGGED_IN_SALT\',   \'put your unique phrase here\')',
                'define(\'NONCE_SALT\',       \'put your unique phrase here\')',
            ];
        }else{
            $this->replaceReplaceValues = ['SITE_NAME'];
        }
    }

    /**
     * Set WP Nonces/Authentications
     */
    protected function setWpNonces()
    {
        $wpAuth = file_get_contents(env('WP_AUTH_KEY_URL'));
        $wpNonces = explode(';', $wpAuth);

        foreach ($wpNonces as $wpNonce) {
            $this->wpNonces[] = $wpNonce . ';';
        }
    }

    protected function initCommand()
    {
        $this->directories = [
            'bash'      => env('DIRECTORY_BASH_SCRIPTS'),
            'nginx'     => env('DIRECTORY_NGINX'),
            'php'       => env('DIRECTORY_PHP'),
            'templates' => env('DIRECTORY_TEMPLATES'),
            'web'       => env('DIRECTORY_WEB'),
            'public'    => env('DIRECTORY_PUBLIC'),
            'logs'      => env('DIRECTORY_LOGS'),
        ];
    }

    /**
     * Execute the console command
     */
    public function handle()
    {
        $this->initCommand();

        // build a connection to the wordpress database
        $wpConnection = DB::connection('wp_mysql');

        $domains = Domain::all();

        foreach ($domains as $domain) {
            // build the directories
            $logDirectory = $this->directories['web'] . '/' . $domain->username . '/' . $this->directories['logs'];
            $publicDirectory = $this->directories['web'] . '/' . $domain->username . '/' . $this->directories['public'];

            shell_exec($this->directories['bash'] . '/build_dirs.sh ' . $logDirectory . ' ' . $publicDirectory);

            if($domain->isWordPress){
                // grab the correct files
                $this->isWordPress = true;
                $this->setNginxConfigFile();
                $this->setPhpConfigFile();
                $this->setReplaceSearchValues();
                $this->setReplaceReplaceValues($domain->name);

                // install wordpress
                shell_exec($this->directories['bash'] . '/wp_install.sh ' . $publicDirectory);

                // create the wordpress database and user
                $wpConnection->statement('CREATE DATABASE :schema', ['schema' => $this->replaceReplaceValues[1]]);
                $wpConnection->statement('GRANT ALL PRIVILEGES ON :schema TO :user AT :host IDENTIFIED BY :password', [
                    'schema' => $this->replaceReplaceValues[1],
                    'user' => $this->replaceReplaceValues[2],
                    'password' => $this->replaceReplaceValues[3],
                ]);
            }else{
                // grab the correct files
                $this->isWordPress = false;
                $this->setNginxConfigFile();
                $this->setPhpConfigFile();
                $this->setReplaceSearchValues();
                $this->setReplaceReplaceValues($domain->name);
            }

            // modify the nginx and php config files
            $nginxConfig = str_replace($this->replaceSearchValues, $this->replaceReplaceValues, $this->nginxConfigFile);
            $phpConfig = str_replace($this->replaceSearchValues, $this->replaceReplaceValues, $this->phpConfigFile);

            // write the nginx and php config files
            $nginxFile = fopen($this->directories['nginx'] . $domain->name, 'w');
            fwrite($nginxFile, $nginxConfig);
            fclose($nginxFile);

            $phpFile = fopen($this->directories['php'] . $domain->name . '.conf', 'w');
            fwrite($phpFile, $phpConfig);
            fclose($phpFile);

            // soft-delete the record
            $domain->delete();
        }
    }
}