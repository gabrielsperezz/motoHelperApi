<?php
namespace MotoHelper\Helper\Mongo\Silex\Provider;

class MongoConnectionProvider extends \Pimple
{
    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $provider = $this;
        foreach($options as $key => $connection) {
            $this[$key] = $this->share(function() use($connection, $provider) {
                return $provider->createConnection($connection['server'], $connection['options'], $connection['database']);
            });
        }
    }

    /**
     * @param string $server
     * @param array $options
     * @param string $database
     * @return \Mongo|\MongoClient
     */
    public function createConnection($server = "mongodb://localhost:27017", array $options = array("connect" => true), $database = "default")
    {
        $mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';
        $mongoInstance = new $mongoClass($server, $options);

        return $mongoInstance->selectDB($database);
    }
}