<?php

namespace Tests\App\Command;

use App\Entity\User;
use AppBundle\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use App\Command\UserCreateCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\App\AbstractTestCase;

class UserCreateCommandTest extends AbstractTestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var CreateUserCommand
     */
    private $command;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(
            new UserCreateCommand(
                $this->getManager(),
                $this->get('validator')
            )
        );

        $this->command = $application->find('user:create');
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute(): void
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'email' => $email = 'kunicmarko20@gmail.com',
            'password' => 'randompassword123',
            'role' => 'ROLE_USER'
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains('Created user kunicmarko20@gmail.com', $output);

        $user = $this->find(User::class, 2);

        $this->assertSame($email, $user->getEmail());
    }

    public function testInvalidEmail(): void
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'email' => $email = 'kunicmarko20',
            'password' => 'randompassword123',
            'role' => 'ROLE_USER'
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains('This value is not a valid email address.', $output);

        $this->assertNull($this->find(User::class, 2));
    }

    public function testDuplicateEmail(): void
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'email' => $email = 'moderator@mod.com',
            'password' => 'randompassword123',
            'role' => 'ROLE_USER'
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains('Email is already taken.', $output);

        $this->assertNull($this->find(User::class, 2));
    }

    public function testPasswordLengthTooShort(): void
    {
        $this->commandTester->execute([
            'command'  => $this->command->getName(),
            'email' => $email = 'kunicmarko20@gmail.com',
            'password' => 'ran',
            'role' => 'ROLE_USER'
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains('Your password must be at least 6 characters long.', $output);

        $this->assertNull($this->find(User::class, 2));
    }
}
