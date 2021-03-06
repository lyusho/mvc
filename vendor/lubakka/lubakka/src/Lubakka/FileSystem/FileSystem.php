<?php
/**
 * Created by PhpStorm.
 * User: lboykov
 * Date: 15-1-25
 * Time: 18:11
 */

namespace Lubakka\FileSystem;

use Lubakka\Debug\Debug;

class FileSystem
{

    public function __construct()
    {

    }

    public static function mkdir($dir)
    {
        $realPath = realpath($dir);
        if (!$realPath) {
            umask(0000);
            if (false === @mkdir($dir)) {
                throw new \RuntimeException(sprintf('Unable to create the %s directory', $dir));
            }

        }
    }

    public function deleteDir($dirPath)
    {
        if ($dirPath != dirname(dirname(dirname(CONF_PATH))) . DS . 'conf' . DS . 'cache') {
            if (!is_dir($dirPath)) {
                throw new \InvalidArgumentException(sprintf('"%s" must be a directory.', $dirPath));
            }
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file) && !is_link($file)) {
                    if (CONF_PATH . Debug::$cacheDir != $file) {
                        $this->deleteDir($file);
                    }
                } else {
                    if (true !== @unlink($file)) {
                        throw new \Exception(sprintf('Failed to remove file "%s".', $file));
                    }
                }
            }
            if ('\\' === DIRECTORY_SEPARATOR && is_dir($dirPath)) {
                if (true !== @rmdir($dirPath)) {
                    throw new \Exception(sprintf('Failed to remove file "%s".', $dirPath));
                }
            }
//            if (true !== @rmdir($dirPath)) {
//                throw new \Exception(sprintf('Failed to remove directory "%s".', $dirPath));
//            }
        }
    }

    public function deleteFile($file)
    {
        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('"%s" must be a file.', $file));
        }
        if (true !== @unlink($file)) {
            throw new \Exception(sprintf('Failed to remove file "%s".', $file));
        }
        return $this;
    }
}