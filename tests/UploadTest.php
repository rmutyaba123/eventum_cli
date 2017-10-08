<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the LICENSE and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\Console\Test;

use Eventum\Console\Application;
use Eventum\Console\Command\AddAttachmentCommand;
use Eventum_RPC_Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UploadTest extends TestCase
{
    /** @var Command */
    private $command;

    /** @var CommandTester */
    private $tester;

    public function setUp()
    {
        $application = new Application();
        $application->add(new AddAttachmentCommand());

        $this->command = $application->find('add-attachment');
        $this->tester = new CommandTester($this->command);
    }

    public function testFileUpload()
    {
        $input = array('issue_id' => '2', 'file' => __FILE__);
        $this->tester->execute(
            array_merge(array('command' => $this->command->getName()), $input)
        );

        $this->assertRegExp(
            "{File {$input['file']} uploaded}", $this->tester->getDisplay()
        );
    }

    public function testUploadEmptyFile()
    {
        $input = array('issue_id' => '1', 'file' => '/dev/null');

        try {
            $this->tester->execute(
                array_merge(array('command' => $this->command->getName()), $input)
            );
            $this->fail();
        } catch (Eventum_RPC_Exception $e) {
            $this->assertEquals('Empty file uploaded', $e->getMessage());
        }
    }
}
