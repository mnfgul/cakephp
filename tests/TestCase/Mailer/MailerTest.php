<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         3.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Mailer;

use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\TestSuite\TestCase;

class TestMailer extends Mailer
{
    public function getEmailForAssertion()
    {
        return $this->_email;
    }

}

class MailerTest extends TestCase
{
    public function getMockForEmail($methods = [], $args = [])
    {
        return $this->getMock('Cake\Mailer\Email', (array)$methods, (array)$args);
    }

    public function testConstructor()
    {
        $mailer = new TestMailer();
        $this->assertInstanceOf('Cake\Mailer\Email', $mailer->getEmailForAssertion());
        $this->assertEquals('test', $mailer->layout);
    }

    public function testReset()
    {
        $mailer = new TestMailer();
        $email = $mailer->getEmailForAssertion();

        $mailer->set(['foo' => 'bar']);
        $this->assertNotEquals($email->viewVars(), $mailer->reset()->getEmailForAssertion()->viewVars());
    }

    public function testGetName()
    {
        $result = (new TestMailer())->getName();
        $expected = 'Test';
        $this->assertEquals($expected, $result);
    }

    public function testLayout()
    {
        $result = (new TestMailer())->layout('foo');
        $this->assertInstanceOf('Cake\Test\TestCase\Mailer\TestMailer', $result);
        $this->assertEquals('foo', $result->layout);
    }

    public function testProxies()
    {
        $email = $this->getMockForEmail('setHeaders');
        $email->expects($this->once())
            ->method('setHeaders')
            ->with([]);
        $result = (new TestMailer($email))->setHeaders([]);
        $this->assertInstanceOf('Cake\Test\TestCase\Mailer\TestMailer', $result);

        $email = $this->getMockForEmail('addHeaders');
        $email->expects($this->once())
            ->method('addHeaders')
            ->with([]);
        $result = (new TestMailer($email))->addHeaders([]);
        $this->assertInstanceOf('Cake\Test\TestCase\Mailer\TestMailer', $result);

        $email = $this->getMockForEmail('attachments');
        $email->expects($this->once())
            ->method('attachments')
            ->with([]);
        $result = (new TestMailer($email))->attachments([]);
        $this->assertInstanceOf('Cake\Test\TestCase\Mailer\TestMailer', $result);
    }

    public function testSet()
    {
        $email = $this->getMockForEmail('viewVars');
        $email->expects($this->once())
            ->method('viewVars')
            ->with(['key' => 'value']);
        $result = (new TestMailer($email))->set('key', 'value');
        $this->assertInstanceOf('Cake\Test\TestCase\Mailer\TestMailer', $result);

        $email = $this->getMockForEmail('viewVars');
        $email->expects($this->once())
            ->method('viewVars')
            ->with(['key' => 'value']);
        $result = (new TestMailer($email))->set(['key' => 'value']);
        $this->assertInstanceOf('Cake\Test\TestCase\Mailer\TestMailer', $result);
    }

    public function testSend()
    {
        $email = $this->getMockForEmail('send');
        $email->expects($this->any())
            ->method('send')
            ->will($this->returnValue([]));

        $mailer = $this->getMock('Cake\Test\TestCase\Mailer\TestMailer', ['test'], [$email]);
        $mailer->expects($this->once())
            ->method('test')
            ->with('foo', 'bar');

        $mailer->send('test', ['foo', 'bar']);
    }

    /**
     * @expectedException Cake\Mailer\Exception\MissingActionException
     * @expectedExceptionMessage Mail TestMailer::test() could not be found, or is not accessible.
     */
    public function testMissingActionThrowsException()
    {
        (new TestMailer())->send('test');
    }
}