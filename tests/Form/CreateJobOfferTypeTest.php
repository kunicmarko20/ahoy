<?php

namespace Tests\App\Form;

use App\Form\CreateJobOfferType;
use Symfony\Component\Form\Test\TypeTestCase;
use App\DTO;

class CreateJobOfferTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'title' => $title = 'faketitle',
            'description' => $description = 'fakeDescription',
            'email' => $email = 'fakeEmail',
        ];

        $form = $this->factory->create(
            CreateJobOfferType::class,
            $object = new DTO\CreateJobOffer()
        );

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object->title, $title);
        $this->assertEquals($object->description, $description);
        $this->assertEquals($object->email, $email);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
