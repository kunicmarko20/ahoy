<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

class UserCreateCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create a user.')
            ->setHelp($this->getHelpText())
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('role', InputArgument::OPTIONAL, 'Set the user role', 'ROLE_MODERATOR'),
            ]);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = User::create(
            $input->getArgument('email'),
            $input->getArgument('password'),
            $input->getArgument('role')
        );

        if (count($errors = $this->validator->validate($user, null, ['Default', 'create'])) > 0) {
            $output->writeln((string) $errors);
            return;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $user->getEmail()));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }
                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }
                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function getHelpText()
    {
        return <<<EOT
The <info>user:create</info> command creates a user:
  <info>php bin/console user:create matthieu@example.com</info>
This interactive shell will ask you for an password.
You can alternatively specify the email and password as the first and second arguments:
  <info>php bin/console user:create matthieu@example.com mypassword</info>
You can create a super admin via the super-admin flag:
  <info>php bin/console user:create matthieu@example.com mypassword ROLE_MODERATOR</info>
EOT;
    }
}
