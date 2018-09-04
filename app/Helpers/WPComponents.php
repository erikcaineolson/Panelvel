<?php namespace App\Helpers;

/**
 * Class WPComponents
 * @package App\Helpers
 */
class WPComponents
{
    const UNWANTED_CHARACTERS = '(\W+)|(_+)';

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var string
     */
    private $databasePassword;

    /**
     * @var string
     */
    private $databaseUser;

    /**
     * @var bool
     */
    private $isSubdirectory;

    /**
     * @var string
     */
    private $siteName;

    private function setDatabaseName()
    {
        $this->databaseName = 'wp_' . preg_replace('/' . self::UNWANTED_CHARACTERS . '/', '', strtolower($this->siteName));
    }

    private function setDatabasePassword()
    {
        $this->databasePassword = file_get_contents(env('PASSWORD_GENERATOR_URL'));
    }

    private function setDatabaseUser()
    {
        $username = 'wpu_';
        $tempSiteName = preg_replace('/' . self::UNWANTED_CHARACTERS . '/', '|', strtolower($this->siteName));
        $siteNamePieces = explode('|', $tempSiteName);

        foreach ($siteNamePieces as $siteNamePiece) {
            $username .= substr($siteNamePiece, 0, 4);
        }

        $this->databaseUser = substr($username, 0, 16);
    }

    /**
     * WPComponents constructor.
     *
     * @param      $siteName
     * @param bool $isSubdirectory
     */
    public function __construct($siteName, $isSubdirectory = false)
    {
        $this->isSubdirectory = $isSubdirectory;
        $this->siteName = $siteName;

        $this->setDatabaseName();
        $this->setDatabasePassword();
        $this->setDatabaseUser();
    }

    /**
     * Get databaseName
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Get databasePassword
     *
     * @return string
     */
    public function getDatabasePassword()
    {
        return $this->databasePassword;
    }

    /**
     * Get databaseUser
     *
     * @return string
     */
    public function getDatabaseUser()
    {
        return $this->databaseUser;
    }
}
