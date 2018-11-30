<?php defined('SYSPATH') or die('No direct script access.');

class System
{
    /**
     * обновляем версию
     */
    public static function versionRefresh()
    {
        $version = time();

        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR .'version.php', 'w');

        fwrite($fp, '<?php defined("SYSPATH") or die("No direct script access");');
        fwrite($fp, 'return array(');
        fwrite($fp, '   "hash" => "'. $version .'",');
        fwrite($fp, ');');
        fclose($fp);

        return $version;
    }

    /**
     * сборка frontend
     *
     * @param $command
     * @return mixed
     */
    public static function gulp($command)
    {
        switch ($command) {
            case 'build':
            case 'fast':
            case 'images':
                break;
            default:
                return 'Wrong gulp command';
        }

        exec('gulp ' . $command, $output);

        return $output;
    }

    /**
     * сборка backend
     *
     * @return string
     */
    public static function git()
    {
        return 'Under construction';
    }
}