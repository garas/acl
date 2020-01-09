<?php

namespace Acl\Test\TestCase;

use Acl\Model\Table\AcosTable;
use Acl\Shell\AclShell;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @property \PHPUnit\Framework\MockObject\MockObject&\Cake\Console\ConsoleIo $io
 * @property \PHPUnit\Framework\MockObject\MockObject&\Acl\Shell\AclShell $Shell
 * @property \Acl\Model\Table\AcosTable $Acos
 */
class AclShellTest extends TestCase
{
    public $fixtures = [
        'app.Acos',
        'app.Aros',
        'app.ArosAcos',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder(ConsoleIo::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->Shell = $this->getMockBuilder(AclShell::class)
            ->setMethods(['in', 'out', 'hr', 'err', '_stop'])
            ->setConstructorArgs([$this->io])
            ->getMock();
        Configure::write('Acl.classname', 'DbAcl');
        Configure::write('Acl.database', 'test');
        $this->Acos = TableRegistry::getTableLocator()->get(AcosTable::class);
        $this->Shell->startup();
    }

    public function testCreateDeleteNode()
    {
        $this->Shell->args = ['create', 'aco', 'ROOT', 'Controller3'];
        $this->Shell->runCommand($this->Shell->args);

        $node = $this->Acos->node('ROOT/Controller3')->first();
        $this->assertNotEmpty($node);

        $this->Shell->args = ['delete', 'aco', 'ROOT/Controller3'];
        $this->Shell->runCommand($this->Shell->args);

        $node = $this->Acos->node('ROOT/Controller3');
        $this->assertEmpty($node);
    }

    public function testSetParentNode()
    {
        $this->Shell->args = ['create', 'aco', 'ROOT', 'Parent'];
        $this->Shell->runCommand($this->Shell->args);

        $this->Shell->args = ['create', 'aco', 'ROOT', 'Child'];
        $this->Shell->runCommand($this->Shell->args);

        $this->Shell->args = ['setparent', 'aco', 'Child', 'Parent'];
        $this->Shell->runCommand($this->Shell->args);

        $node = $this->Acos->node('Parent/Child')->first();
        $this->assertNotEmpty($node);
    }
}
