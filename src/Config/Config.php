<?php
declare(strict_types=1);

namespace TzDate\Config;

use RuntimeException;

class Config
{
    private static array $additionalConfig = [];
    private array $config = [];

    public static function setAdditionalConfig(array $additionalConfig): void
    {
        self::$additionalConfig = $additionalConfig;
    }

    /**
     * @throws \JsonException
     */
    public function __construct()
    {
        $this->loadConfig();
    }

    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        throw new RuntimeException(sprintf(
            '%s does not exist in the configuration', $name
        ));
    }

    /**
     * @return void
     * @throws \JsonException
     */
    private function loadConfig(): void
    {
        $configs = [];
        foreach ([
                     __DIR__ . '/../../res/config.json.dist',
                     __DIR__ . '/../../res/config.json',
                 ] as $file) {
            if (!file_exists($file)) {
                continue;
            }
            $configs[] = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        }
        $configs[] = self::$additionalConfig;

        foreach ($configs as $config) {
            foreach ($config as $k => $v) {
                if (($k === 'cities' || $k === 'aliases') && array_key_exists($k, $this->config)) {
                    $this->config[$k] = array_merge($this->config[$k], $v);
                } else {
                    $this->config[$k] = $v;
                }
            }
        }
    }
}
