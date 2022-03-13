<?php

namespace App\Service;

final class FileSystemService
{
    public static function delete(string $path, string $name = null): void
    {
        if (self::exist($path, $name)) {
            unlink(sprintf('%s%s', $path, $name));
        }
    }

    public static function exist(string $path, string $name = null): bool
    {
        if (file_exists(sprintf('%s%s', $path, $name))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public static function find(string $path, ?string $name)
    {
        if (!(self::exist($path, null))) {
            return false;
        }

        $scandir = scandir($path);

        if (false === $scandir) {
            return false;
        }

        foreach ($scandir as $file) {
            if ($name == $file) {
                return sprintf('%s%s', $path, $file);
            }
        }

        $scandir = scandir($path);

        if (false === $scandir) {
            return false;
        }

        foreach ($scandir as $file) {
            if (preg_match($name ?? '', $file)) {
                return sprintf('%s%s', $path, $file);
            }
        }

        return false;
    }

    public static function download(?string $url, string $name, string $path): void
    {
        if (is_null($url)) {
            return;
        }
        self::createDirectoryIfDontExist($path);
        file_put_contents(sprintf('%s%s', $path, $name), fopen($url, 'r'));
    }

    public static function createDirectoryIfDontExist(string $path): void
    {
        if (!(self::exist($path, null))) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * @return bool|string
     */
    public static function unzip(string $zipfile, string $extractPath)
    {
        $zip = new \ZipArchive();

        if ('true' != $zip->open($zipfile)) {
            return false;
        }

        $unzip = $zip->getNameIndex(0);

        $zip->extractTo($extractPath);
        $zip->close();

        return $unzip;
    }

    /**
     * @return false|string
     */
    public static function getFile(string $path, string $name = null)
    {
        if (self::exist($path, $name)) {
            return file_get_contents(sprintf('%s%s', $path, $name));
        }

        return false;
    }

    /**
     * @return string
     */
    public static function stripAccents(string $str)
    {
        $str = preg_replace('/[^a-zA-Z0-9\\s]/', '', $str);

        return strtr(utf8_decode($str ?? ''), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
