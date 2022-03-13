<?php

namespace App\Service;

use App\Common\Exception\DotEnvException;
use Symfony\Component\Dotenv\Dotenv as DotEnvBundle;

final class DotEnvService
{
    public const DEFAULT_ENV = __DIR__ . '/../../.env';
    public const LOCAL_ENV = __DIR__ . '/../../.env.local';

    /**
     * @throws DotEnvException
     */
    public function findByParameter(string $parameter): string
    {
        $env = self::LOCAL_ENV;
        if (false === file_exists($env)) {
            $env = self::DEFAULT_ENV;
        }

        $dotenv = new DotEnvBundle();
        $dotenv->loadEnv($env);

        if (false === array_key_exists($parameter, $_ENV) && self::DEFAULT_ENV == $env) {
            throw new DotEnvException(sprintf('missing parameter `%s` in `%s` file.', $parameter, $env));
        }

        if (false === array_key_exists($parameter, $_ENV) && self::LOCAL_ENV == $env) {
            $env = self::DEFAULT_ENV;
            $dotenv->loadEnv($env);
            if (false === array_key_exists($parameter, $_ENV)) {
                throw new DotEnvException(sprintf('missing parameter `%s` in `%s` file.', $parameter, $env));
            }
        }

        return $_ENV[$parameter];
    }

    /**
     * @return array<string, mixed>
     */
    public function find(): array
    {
        $env = self::LOCAL_ENV;
        if (false === file_exists($env)) {
            $env = self::DEFAULT_ENV;
        }

        $dotenv = new DotEnvBundle();
        $dotenv->loadEnv($env);

        return $_ENV;
    }
}
