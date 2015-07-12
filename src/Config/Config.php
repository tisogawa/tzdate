<?php

namespace TzDate\Config;

class Config
{
    /** @var array */
    private static $additionalConfig = array();

    /** @var array */
    private $config = array();

    /**
     * @param array $additionalConfig
     */
    public static function setAdditionalConfig(array $additionalConfig)
    {
        self::$additionalConfig = $additionalConfig;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        throw new \RuntimeException(sprintf(
            '%s does not exist in the configuration'
        ));
    }

    /**
     *
     */
    private function loadConfig()
    {
        $config_dir = __DIR__ . '/../../res';
        $files = array(
            $config_dir . '/config.json.dist',
            $config_dir . '/config.json',
        );
        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }
            $config = json_decode(file_get_contents($file), true);
            if (!is_array($config)) {
                throw new \RuntimeException(sprintf(
                    'Failed reading configuration from %s', $file
                ));
            }
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, self::$additionalConfig);
    }
}
