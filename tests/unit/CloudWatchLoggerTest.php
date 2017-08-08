<?php

namespace LegalThings;

use Codeception\TestCase\Test;
use Maxbanton\Cwh\Handler\CloudWatch as CloudWatchHandler;
use Monolog\Logger;

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
            'instance_name' => 'instance_name',
            'channel_name' => 'channel_name'
        ];
    }
    
    
    public function testConstruct()
    {
        $config = $this->getConfig();
        $logger = new CloudWatchLogger($config);
        
        $this->assertEquals((object)$config, $logger->config);
        $this->assertInstanceOf(Logger::class, $logger->logger);
        
        $handlers = $logger->logger->getHandlers();
        $this->assertInstanceOf(CloudWatchHandler::class, $handlers[0]);
    }
    
    public function testLog()
    {
        $config = $this->getConfig();
        
        $monolog = $this->getMockBuilder(Logger::class)
                ->setMethods(['info', 'notice', 'warn', 'error', 'debug'])
                ->disableOriginalConstructor()
                ->getMock();
        $monolog->expects($this->once())->method('info')->with('test_info', ['hello' => 'world']);
        $monolog->expects($this->once())->method('notice')->with('test_notice', ['foo' => 'bar']);
        $monolog->expects($this->once())->method('warn')->with('test_warn', ['flag' => true]);
        $monolog->expects($this->once())->method('error')->with('test_error', ['line' => 111]);
        $monolog->expects($this->once())->method('debug')->with('test_debug', ['debugging' => false]);
        
        $logger = new CloudWatchLogger($config, $monolog);
        
        $logger->info('test_info', ['hello' => 'world']);
        $logger->notice('test_notice', ['foo' => 'bar']);
        $logger->warn('test_warn', ['flag' => true]);
        $logger->error('test_error', ['line' => 111]);
        $logger->debug('test_debug', ['debugging' => false]);
    }
}
