<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 01/12/2017
 * Time: 16:50.
 */

namespace LahthonyOTPAuthBundle\Tests\Service;

use LahthonyOTPAuthBundle\Service\TwigMailGenerator;
use PHPUnit\Framework\TestCase;
use Swift_Message;
use Twig_Environment;

class TwigMailGeneratorTest extends TestCase
{
    public function testGetMessage()
    {
        $twigEnvironment = $this->getMockBuilder(Twig_Environment::class)
            ->setMethods(['render'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $twigEnvironment
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('@LahthonyOTPAuth/Default/mail.twig'),
                $this->equalTo([
                    'qrcode' => 'jhsdfjhsdkjfhkjdsfh',
                    'otpkey' => 'otpkey',
                ])
            )
            ->willReturn('<h1>hello</h1>>')
        ;

        $twigMailGenerator = new TwigMailGenerator($twigEnvironment);

        $message = $twigMailGenerator->getMessage('jhsdfjhsdkjfhkjdsfh', 'otpkey');
        $this->assertInstanceOf(Swift_Message::class, $message);
    }
}
