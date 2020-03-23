<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Update\Queue;

class JobUpdateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Update\Status
     */
    private $status;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Composer\MagentoComposerApplication
     */
    private $composerApp;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Update\Queue
     */
    private $queue;

    public function setUp()
    {
        $this->status = $this->getMockBuilder('Magento\Update\Status')
            ->disableOriginalConstructor()
            ->getMock();
        $this->composerApp = $this->getMockBuilder('Magento\Composer\MagentoComposerApplication')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queue = $this->getMockBuilder('Magento\Update\Queue')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $jobUpdate = new \Magento\Update\Queue\JobUpdate(
            'setup:upgrade',
            ['components' => [['name' => 'vendor/package', 'version' => '1.0']]],
            $this->queue,
            $this->composerApp,
            $this->status
        );

        $this->status->expects($this->atLeastOnce())->method('add');
        $this->composerApp->expects($this->at(0))
            ->method('runComposerCommand')
            ->with(['command' => 'require', 'packages' => ['vendor/package 1.0'], '--no-update' => true])
            ->willReturn('Success');
        $this->composerApp->expects($this->at(1))
            ->method('runComposerCommand')
            ->with(['command' => 'update'])
            ->willReturn('Success');
        $this->queue->expects($this->once())->method('addJobs')->with([['name' => 'setup:upgrade', 'params' => []]]);
        $jobUpdate->execute();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot find component to update
     */
    public function testExecuteNoRequire()
    {
        $jobUpdate = new \Magento\Update\Queue\JobUpdate(
            'setup:upgrade',
            [],
            $this->queue,
            $this->composerApp,
            $this->status
        );
        $this->composerApp->expects($this->never())->method('runComposerCommand');
        $this->queue->expects($this->never())->method('addJobs');
        $jobUpdate->execute();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Exception
     */
    public function testExecuteException()
    {
        $jobUpdate = new \Magento\Update\Queue\JobUpdate(
            'setup:upgrade',
            ['components' => [['name' => 'vendor/package', 'version' => '1.0']]],
            $this->queue,
            $this->composerApp,
            $this->status
        );
        $this->status->expects($this->atLeastOnce())->method('add');
        $this->composerApp->expects($this->at(0))
            ->method('runComposerCommand')
            ->with(['command' => 'require', 'packages' => ['vendor/package 1.0'], '--no-update' => true])
            ->willReturn('Success');
        $this->composerApp->expects($this->at(1))
            ->method('runComposerCommand')
            ->with(['command' => 'update'])
            ->will($this->throwException(new \Exception('Exception')));
        $this->status->expects($this->once())->method('setUpdateError')->with(true);
        $jobUpdate->execute();
    }
}
