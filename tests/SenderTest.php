<?php

namespace Disc\Zabbix\Tests;

use AspectMock\Kernel;
use AspectMock\Test;
use Disc\Zabbix\Sender;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use PHPUnit_Framework_TestCase;

/**
 * Class SenderTest
 * @package \Disc\Zabbix\Tests
 *
 * @covers \Disc\Zabbix\Sender
 * @coversDefaultClass \Disc\Zabbix\Sender
 */
class SenderTest extends PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Prepare
     */
    public function setUp()
    {
        $kernel = Kernel::getInstance();
        $kernel->init([
            'debug' => true,
            'includePaths' => [__DIR__.'/../src']
        ]);
    }

    /**
     * Test for getData
     *
     * @covers ::getData
     * @covers ::send
     */
    public function testAddData()
    {
        /** @var Mock|Sender $sender */
        $sender = \Mockery::mock(Sender::class . '[sendData]', ['localhost']);
        $sender->shouldAllowMockingProtectedMethods();
        $sender->shouldReceive('sendData')->once()->with(json_encode([
            'request' => 'sender data',
            'data' => [],
        ]));

        $sender->send();

        $sender->addData('Host', 'test.key', 'some value');
        $sender->addData('Host', 'another.key', 123);
        $expectedTime = time();
        $sender->addData('Host', 'one.more.key', 0.001, $expectedTime);

        $sender->shouldReceive('sendData')->once()->with(json_encode([
            'request' => 'sender data',
            'data' => [
                [
                    'host' => 'Host',
                    'key' => 'test.key',
                    'value' => 'some value',
                ],
                [
                    'host' => 'Host',
                    'key' => 'another.key',
                    'value' => 123,
                ],
                [
                    'host' => 'Host',
                    'key' => 'one.more.key',
                    'value' => 0.001,
                    'clock' => $expectedTime,
                ],
            ],
        ]));
        $sender->send();
    }

    /**
     * Test for clear data
     *
     * @covers ::getData
     * @covers ::clearData
     * @covers ::send
     */
    public function testClearData()
    {
        /** @var Mock|Sender $sender */
        $sender = \Mockery::mock(Sender::class . '[sendData]', ['localhost']);
        $sender->shouldAllowMockingProtectedMethods();

        $sender->addData('Host', 'test.key', 'some value');
        $sender->shouldReceive('sendData')->once()->with(json_encode([
            'request' => 'sender data',
            'data' => [
                [
                    'host' => 'Host',
                    'key' => 'test.key',
                    'value' => 'some value',
                ]
            ],
        ]));
        $sender->send();
        $sender->shouldReceive('sendData')->once()->with(json_encode([
            'request' => 'sender data',
            'data' => [],
        ]));
        $sender->send();
    }

    /**
     * Test for getResponse
     *
     * @covers ::getResponse
     */
    public function testGetResponse()
    {
        test::func('Disc\Zabbix', 'socket_create', '');
        test::func('Disc\Zabbix', 'socket_connect', '');
        test::func('Disc\Zabbix', 'socket_send', '');
        test::func('Disc\Zabbix', 'socket_close', '');

        /** @var Mock|Sender $sender */
        $sender = \Mockery::mock(Sender::class)->makePartial();
        $sender->shouldAllowMockingProtectedMethods();
        $sender->shouldReceive('socketReceive')->andReturn('header       {"code": 100}');
        $sender->send();
        $this->assertSame(["code" => 100], $sender->getResponse());
    }
}
