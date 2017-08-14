<?php

namespace LegalThings;

use Codeception\TestCase\Test;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;

/**
 * Tests for CloudWatchLogger class
 * 
 * @covers \LegalThings\CloudWatchLogger
 */
class CloudWatchLoggerTest extends Test
{
    protected function getConfig()
    {
        return [
            'aws' => [
                'version' => 'latest',
                'region' => 'eu-west-1',
                'credentials' => [
                    'key' => 'fake_key',
                    'secret' => 'fake_secret'
                ]
            ],
            'group_name' => 'group_name',
            'stream_name' => 'stream_name',
            'options' => [
                'retention_days' => 90,
                'error_max_retry' => 3,
                'error_retry_delay' => 0
            ]
        ];
    }
    
    
    public function testConstruct()
    {
        $config = $this->getConfig();
        
        $logger = new CloudWatchLogger($config);
        
        $expected = (object)$config;
        $expected->options = (object)$expected->options;
        $this->assertEquals($expected, $logger->config);
        
        $this->assertInstanceOf(CloudWatchClient::class, $logger->client);
    }
    
    
    public function testLog()
    {
        $config = $this->getConfig();
        $data = ['foo' => 'bar', 'number' => 10, 'flagged' => false];
        
        $client = $this->getMockBuilder(CloudWatchClient::class)
            ->disableOriginalConstructor()->setMethods(['log'])->getMock();
        
        $client->expects($this->once())->method('log')->with(
            $data,
            $config['group_name'],
            $config['stream_name'],
            (object)$config['options']
        );
        
        $logger = new CloudWatchLogger($config, $client);
        
        $logger->log($data);
    }
    
    /**
     * @expectedException Aws\CloudWatchLogs\Exception\CloudWatchLogsException
     */
    public function testLogRetryOnError()
    {
        $config = $this->getConfig();
        $data = ['foo' => 'bar', 'number' => 10, 'flagged' => false];
        
        $exception = $this->getMockBuilder(CloudWatchLogsException::class)
            ->disableOriginalConstructor()->setMethods(['getAwsErrorCode'])->getMock();
        $exception->expects($this->exactly(4))->method('getAwsErrorCode')->willReturn('InvalidSequenceTokenException');
        
        $client = $this->getMockBuilder(CloudWatchClient::class)
            ->disableOriginalConstructor()->setMethods(['log'])->getMock();
        $client->expects($this->exactly(4))->method('log')->willThrowException($exception);
        
        $logger = new CloudWatchLogger($config, $client);
        
        $logger->log($data);
    }
}
