<?php

namespace LegalThings;

use InvalidArgumentException;
use Aws\Result;

/**
 * Class that facilitates CloudWatch logging
 */
class CloudWatchLogger
{
    /**
     * @var object
     */
    public $config;
    
    /**
     * @var CloudWatchClient
     */
    public $client;
    
    
    /**
     * Class constructor
     * 
     * @param object|array     $config
     * @param CloudWatchClient $client
     */
    public function __construct($config, $client = null)
    {
        $this->config = (object)$config;
        
        $this->client = $client ?: $this->create($this->config);
    }
    
    /**
     * Create a client
     * 
     * @param object $config
     * 
     * @return CloudWatchClient
     */
    protected function create($config)
    {
        if (isset($config->instance_name)) {
            $config->stream_name = $config->instance_name; // for bc
        }
        
        $this->validateConfig($config);
        
        return new CloudWatchClient($config);
    }
    
    /**
     * Validate config
     * 
     * @param object $config
     */
    protected function validateConfig($config)
    {
        if (!isset($config->aws)) {
            throw new InvalidArgumentException('CloudWatchLogger config \'aws\' not given');
        }
        
        if (!isset($config->group_name)) {
            throw new InvalidArgumentException('CloudWatchLogger config \'group_name\' not given');
        }
        
        if (!isset($config->stream_name)) {
            throw new InvalidArgumentException('CloudWatchLogger config \'stream_name\' not given');
        }
    }
    
    
    /**
     * Log data to CloudWatch
     * 
     * @param string|array|object $data
     * 
     * @return Result
     */
    public function log($data)
    {
        try {
            $result = $this->client->log($data, $this->config->group_name, $this->config->stream_name);
            return $result;
        } catch (\Exception $e) {
            // @todo handle errors
            throw $e;
//            $message = $e->getMessage();
//            $e;
        }
    }
}
