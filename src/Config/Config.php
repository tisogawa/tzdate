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
        $config_dir = __DIR__ . '/../../res';
        $files = [
            $config_dir . '/config.json.dist',
            $config_dir . '/config.json',
        ];
        $options = [$this->config];
        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }
            $config = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($config)) {
                throw new RuntimeException(sprintf(
                    'Failed reading configuration from %s', $file
                ));
            }
            $options[] = $config;
        }
        $options[] = self::$additionalConfig;
        $this->config = array_merge(...$options);
    }
}
