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
        $this->nginxConfigFile = file_get_contents($this->directories['templates'] . $nginxConfigFilename);
    }

    /**
     * Set PHP config file
     */
    protected function setPhpConfigFile()
    {
        $phpConfigFilename = env('TEMPLATE_PHP');
        $this->phpConfigFile = file_get_contents($this->directories['nginx'] . $phpConfigFilename);
    }

    /**
     * Set replacement values
     */
    protected function setReplaceReplaceValues($domainName)
    {
        if ($this->isWordPress) {
            $this->setWpNonces();

            $this->replaceSearchValues = [
                $domainName,
                'wp_' . substr(str_replace('.', '', $domainName), 6) . date('ymd'),
                'wp_' . substr(str_replace('.', '', $domainName), 6) . date('ymd') . 'U',
                '' . str_random(24),
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
        } else {
            $this->replaceReplaceValues = [$domainName];
        }
    }

    /**
     * Set search values
     */
    protected function setReplaceSearchValues()
    {
        if ($this->isWordPress) {
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
        } else {
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
            unset($siteInformation);

            // build the site config files from the template files
            $phpConfigTemplateFile = $this->directories['templates'] . '/' . env('TEMPLATE_PHP');
            $phpConfigTemplate = fopen($phpConfigTemplateFile, 'r');

            // check for WP and pull the proper template
            if ($domain->is_word_press) {
                $this->isWordPress = true;
                $nginxConfigTemplate = fopen($this->directories['templates'] . '/' . env('TEMPLATE_WP'), 'r');
            } else {
                $this->isWordPress = false;
                $nginxConfigTemplate = fopen($this->directories['templates'] . '/' . env('TEMPLATE_SITE'), 'r');
            }

            // set search-and-replace
            $this->setReplaceSearchValues();
            $this->setReplaceReplaceValues($domain->name);

            // execute search-and-replace
            $phpConfigFileTemplate = fread($phpConfigTemplate, filesize($phpConfigTemplate));
            $phpConfigFile = str_replace($this->replaceSearchValues, $this->replaceReplaceValues, $phpConfigFileTemplate);
            
            $nginxConfigFileTemplate = fread($nginxConfigTemplate, filesize($nginxConfigTemplate));
            $nginxConfigFile = str_replace($this->replaceSearchValues, $this->replaceReplaceValues, $nginxConfigFileTemplate);

            fclose($phpConfigTemplate);
            fclose($nginxConfigTemplate);

            // generate file names and write (then close) the files
            $phpConfigFileName = $this->directories['php'] . '/' . $domain->name . '.conf';
            $nginxConfigFileName = $this->directories['nginx'] . '/' . $domain->name;

            $phpConfig = fopen($phpConfigFileName, 'w');
            fwrite($phpConfig, $phpConfigFile);
            fclose($phpConfig);

            $nginxConfig = fopen($nginxConfigFileName, 'w');
            fwrite($nginxConfig, $nginxConfigFile);
            fclose($nginxConfig);

            // build the site information string
            //  format: domain name:username:password:is WP
            //  should be: domain.com:domauser:d0m4inP4$s!:true
            $siteInformation = '' . $domain->name . ':' . $domain->username . ':' . $domain->password . ':' . (bool)$domain->is_word_press . "\n";

            fwrite($siteList, $siteInformation);
        }

        fclose($siteList);
    }
}