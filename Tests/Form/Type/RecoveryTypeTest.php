<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 08/12/2017
 * Time: 11:25.
 */

namespace LahthonyOTPAuthBundle\Tests\Form\Type;

use LahthonyOTPAuthBundle\Form\RecoveryType;
use Symfony\Component\Form\Test\TypeTestCase;

class RecoveryTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'email' => 'test@mail.fr',
            'password' => 'test',
        );

        $form = $this->factory->create(RecoveryType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($formData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
