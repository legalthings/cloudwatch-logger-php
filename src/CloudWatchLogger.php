<?php

namespace LegalThings;

use InvalidArgumentException;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Aws\Result;

/**
 * Class that simplifies CloudWatch logging through configuration and a simple interface
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
        $this->config->options = isset($this->config->options) ? (object)$this->config->options : (object)[];
        
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
            throw new InvalidArgumentException("CloudWatchLogger config 'aws' not given");
        }
        
        if (!isset($config->group_name)) {
            throw new InvalidArgumentException("CloudWatchLogger config 'group_name' not given");
        }
        
        if (!isset($config->stream_name)) {
            throw new InvalidArgumentException("CloudWatchLogger config 'stream_name' not given");
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
            $result = $this->client->log($data, $this->config->group_name, $this->config->stream_name, $this->config->options);
            return $result;
        } catch (CloudWatchLogsException $e) {
            return $this->retryLoggingAfterError($e, $data);
        }
    }
    
    /**
     * Retry logging after getting an error, for example invalid sequence token message
     * CloudWatch handles concurrent/simultaneous requests poorly
     * It will throw an exception when the sequence token fetched doesn't match the one its currently at on AWS
     * This can happen when multiple applications need to log and there is no specific order in which this happens
     * To solve this somewhat we retry logging afer fetching a new sequence token, for a limited amount of times
     * 
     * @param CloudWatchLogsException $error
     * @param string|array|object     $data
     * @param int                     $iteration
     * 
     * @return Result
     */
    protected function retryLoggingAfterError($error, $data, $iteration = 0)
    {
        if ($error->getAwsErrorCode() !== 'InvalidSequenceTokenException') {
            // we currently only retry after getting invalid sequence token exception
            throw $error;
        }
        
        $maxRetry = isset($this->config->options->error_max_retry) ? $this->config->options->error_max_retry : 5;
        $retryDelay = isset($this->config->options->error_retry_delay) ? $this->config->options->error_retry_delay : 100000;
        
        if ($iteration >= $maxRetry) {
            throw $error;
        }
        
        try {
            usleep($retryDelay); // delay before logging (if configured) to give other apps a chance to log before retrying
            $result = $this->client->log($data, $this->config->group_name, $this->config->stream_name, $this->config->options);
            return $result;
        } catch (CloudWatchLogsException $e) {
            return $this->retryLoggingAfterError($e, $data, ++$iteration);
        }
        
        throw $error;
    }
}
