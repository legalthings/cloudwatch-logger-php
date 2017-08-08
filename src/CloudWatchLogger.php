<?php

namespace LegalThings;

use Aws\CloudWatchLogs\CloudWatchLogsClient as Client;
use Maxbanton\Cwh\Handler\CloudWatch as Handler;
use Monolog\Formatter\LineFormatter as Formatter;
use Monolog\Logger;

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
     * @var Logger
     */
    public $logger;
    
    
    /**
     * Class constructor
     * 
     * @param object|array $config
     * @param Logger       $logger
     */
    public function __construct($config, $logger = null)
    {
        $this->config = (object)$config;
        
        $this->logger = $logger ?: $this->createLogger($this->config);
    }
    
    /**
     * Create a logger
     * 
     * @param object $config
     * 
     * @return Logger $logger
     */
    protected function createLogger($config)
    {
        $this->validateConfig($config);
        
        $client = new Client((array)$config->aws);
        
        $group = $config->group_name;
        $instance = $config->instance_name;
        $channel = isset($config->channel_name) ? $config->channel_name : null;
        $retention = isset($config->retention_days) ? $config->retention_days : 90;
        $batch = isset($config->batch_size) ? $config->batch_size : 10000;
        $tags = isset($config->tags) ? $config->tags : [];
        $format = isset($config->format) ? $config->format : Formatter::SIMPLE_FORMAT;
        
        $handler = new Handler($client, $group, $instance, $retention, $batch, $tags);
        $formatter = new Formatter($format, null, false, true);
        $handler->setFormatter($formatter);
        
        $logger = new Logger($channel);
        $logger->pushHandler($handler);
        
        return $logger;
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
        
        if (!isset($config->instance_name)) {
            throw new InvalidArgumentException('CloudWatchLogger config \'instance_name\' not given');
        }
    }
    
    
    /**
     * Log info to CloudWatch
     * 
     * @param string $text
     * @param mixed  $data
     */
    public function info($text, $data = [])
    {
        $this->logger->info($text, $data);
    }
    
    /**
     * Log notices to CloudWatch
     * 
     * @param string $text
     * @param mixed  $data
     */
    public function notice($text, $data = [])
    {
        $this->logger->notice($text, $data);
    }
    
    /**
     * Log warnings to CloudWatch
     * 
     * @param string $text
     * @param mixed  $data
     */
    public function warn($text, $data = [])
    {
        $this->logger->warn($text, $data);
    }

    /**
     * Log errors to CloudWatch
     * 
     * @param string $text
     * @param mixed  $data
     */
    public function error($text, $data = [])
    {
        $this->logger->error($text, $data);
    }
    
    /**
     * Log debug to CloudWatch
     * 
     * @param string $text
     * @param mixed  $data
     */
    public function debug($text, $data = [])
    {
        $this->logger->debug($text, $data);
    }
}
