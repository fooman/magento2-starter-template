<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Update\Queue;

class AbstractJobTest extends \PHPUnit\Framework\TestCase
{
    public function testToString()
    {
        /** Any implementation of abstract job can be used for __toString testing */
        $job = new \Magento\Update\Queue\JobBackup(
            'backup',
            ['targetArchivePath' => '/Users/john/archive.zip', 'sourceDirectory' => '/Users/john/Magento']
        );
        $this->assertEquals(
            'backup {"targetArchivePath":"/Users/john/archive.zip","sourceDirectory":"/Users/john/Magento"}',
            (string)$job
        );
    }
}
