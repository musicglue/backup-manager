<?php namespace BackupManager\Databases;

/**
 * Class MysqlDatabase
 * @package BackupManager\Databases
 */
class MysqlDatabase implements Database {

    /** @var array */
    private $config;

    /**
     * @param $type
     * @return bool
     */
    public function handles($type) {
        return strtolower($type) == 'mysql';
    }

    /**
     * @param array $config
     * @return null
     */
    public function setConfig(array $config) {
        $this->config = $config;
    }

    /**
     * @param $outputPath
     * @return string
     */
    public function getDumpCommandLine($outputPath) {
        return sprintf('mysqldump --routines %s --host=%s --port=%s --user=%s --password=%s %s %s > %s',
            $this->config['options'],
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['database']),
            $this->getExcludedTables($this->config['database'], $this->config['exclude']),
            escapeshellarg($outputPath)
        );
    }

    /**
     * @param $inputPath
     * @return string
     */
    public function getRestoreCommandLine($inputPath) {
        return sprintf('mysql --host=%s --port=%s --user=%s --password=%s %s -e "source %s"',
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['database']),
            $inputPath
        );
    }

    private function getExcludedTables($database, $tables)
    {
        $str = "";
        $tableArray = explode(",", $tables);

        foreach ($tableArray as $table) {
            $table = trim($table);
            $str .= "--ignore-table=$database.$table ";
        }

        return $str;
    }

}
