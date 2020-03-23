<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Update\Queue;

class JobComponentUninstallTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Update\Status
     */
    private $status;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Composer\MagentoComposerApplication
     */
    private $composerApp;

    public function setUp()
    {
        $this->status = $this->getMockBuilder('Magento\Update\Status')
            ->disableOriginalConstructor()
            ->getMock();
        $this->composerApp = $this->getMockBuilder('Magento\Composer\MagentoComposerApplication')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $jobComponentUninstall = new \Magento\Update\Queue\JobComponentUninstall(
            'component:uninstall',
            ['components' => [['name' => 'vendor/package']]],
            $this->composerApp,
            $this->status
        );
        $this->status->expects($this->atLeastOnce())->method('add');
        $this->composerApp->expects($this->at(0))
            ->method('runComposerCommand')
            ->with(['command' => 'remove', 'packages' => ['vendor/package'], '--no-update' => true])
            ->willReturn('Success');
        $this->composerApp->expects($this->at(1))
            ->method('runComposerCommand')
            ->with(['command' => 'update'])
            ->willReturn('Success');
        $jobComponentUninstall->execute();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot find component to uninstall
     */
    public function testExecuteNoComponent()
    {
        $jobComponentUninstall = new \Magento\Update\Queue\JobComponentUninstall(
            'component:uninstall',
            [],
            $this->composerApp,
            $this->status
        );
        $this->composerApp->expects($this->never())->method('runComposerCommand');
        $jobComponentUninstall->execute();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Exception
     */
    public function testExecuteException()
    {
        $jobComponentUninstall = new \Magento\Update\Queue\JobComponentUninstall(
            'component:uninstall',
            ['components' => [['name' => 'vendor/package']]],
            $this->composerApp,
            $this->status
        );
        $this->status->expects($this->atLeastOnce())->method('add');
        $this->composerApp->expects($this->at(0))
            ->method('runComposerCommand')
            ->with(['command' => 'remove', 'packages' => ['vendor/package'], '--no-update' => true])
            ->willReturn('Success');
        $this->composerApp->expects($this->at(1))
            ->method('runComposerCommand')
            ->with(['command' => 'update'])
            ->will($this->throwException(new \Exception('Exception')));
        $this->status->expects($this->once())->method('setUpdateError')->with(true);
        $jobComponentUninstall->execute();
    }
}
