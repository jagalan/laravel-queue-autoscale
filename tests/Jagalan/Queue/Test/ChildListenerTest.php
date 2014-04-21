<?php

namespace Jagalan\Queue\Test;

//use Jagalan\Queue\ChildListener;

class ChildListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testItDiesAfterMaxExecutions()
    {
        $process = $this->getMockBuilder('\Symfony\Component\Process\Process', array('run'))
                ->setMethods(array('run'))
                ->disableOriginalConstructor()
                ->getMock();

        $childListener = $this->getMockBuilder('\Jagalan\Queue\ChildListener')
                ->setMethods(array('stop'))
                ->disableOriginalConstructor()
                ->getMock();
        $childListener->expects($this->once())
                ->method('stop')
                ->will($this->returnValue(true));

        // Setting maxExecutions to 2 and running 2 executions should trigger a call to the stop method
        $childListener->setMaxExecutions(2);
        $childListener->runProcess($process, 1024);
        $childListener->runProcess($process, 1024);
    }

    public function testItDoesNotStopIfMaxExecutionIsNotSet()
    {
        $process = $this->getMockBuilder('\Symfony\Component\Process\Process', array('run'))
                ->setMethods(array('run'))
                ->disableOriginalConstructor()
                ->getMock();

        $childListener = $this->getMockBuilder('\Jagalan\Queue\ChildListener')
                ->setMethods(array('stop'))
                ->disableOriginalConstructor()
                ->getMock();
        $childListener->expects($this->never())
                ->method('stop')
                ->will($this->returnValue(true));

        for ($x=0; $x<100; $x++) $childListener->runProcess($process, 1024);
    }
}