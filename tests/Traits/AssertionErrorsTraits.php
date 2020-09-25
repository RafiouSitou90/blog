<?php

namespace App\Tests\Traits;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Trait AssertionErrorsTraits
 * @package App\Tests\Traits
 */
trait AssertionErrorsTraits
{
    /**
     * @param Object $entity
     * @param int $nbError
     *
     * @return void
     */
    public function assertHasErrors(Object $entity, int $nbError = 0): void
    {
        self::bootKernel();
        /** @var ValidatorInterface $validator */
        $validator = self::$container->get('validator');
        $errors = $validator->validate($entity);

        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath(). ' => '. $error->getMessage();
        }
        $this->assertCount($nbError, $errors, implode(', ', $messages));
    }
}
