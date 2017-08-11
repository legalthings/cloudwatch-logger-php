<?php

namespace LegalThings;

use Codeception\TestCase\Test;

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
            'instance_name' => 'instance_name'
        ];
    }
    
    
    public function testConstruct()
    {
        $config = $this->getConfig();
        $logger = new CloudWatchLogger($config);
        
        $expectedConfig = $config + ['stream_name' => 'instance_name']; // for bc
        $this->assertEquals((object)$expectedConfig, $logger->config);
        
        $this->assertInstanceOf(CloudWatchClient::class, $logger->client);
    }
    
    public function testLog()
    {
        $config = $this->getConfig();
        
        $logger = new CloudWatchLogger($config);
        
        $logger->log(['foo' => 'bar', 'number' => 10, 'flagged' => false]);
        
        // @todo: fix up tests
    }
}
