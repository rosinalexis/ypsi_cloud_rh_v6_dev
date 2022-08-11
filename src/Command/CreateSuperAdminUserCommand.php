<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-super-admin-user',
    description: 'Create a super admin user in the application.',
    hidden: false
)]
class CreateSuperAdminUserCommand extends Command
{
    protected static  $defaultName = 'Creates a new super admin user';
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService =$userService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email.')
            ->addArgument('password',InputArgument::REQUIRED,'User plainPassword')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email= $input->getArgument('email');
        $password= $input->getArgument('password');
        $io = new SymfonyStyle($input, $output);


        if (is_null($email))
        {
           $email = $io->ask('user email','admin@cloud-rh.fr');
        }

        if(is_null($password))
        {
           $password = $io->askHidden('user password ?');
        }

        $response =$this->userService->createSuperAdminUser($email,$password);

        if($response['status'])
        {
            $io->success($response['message']);
            $commandeStatus = Command::SUCCESS;
        }else{
            $io->error($response['message']);
            $commandeStatus = Command::FAILURE;
        }

        return $commandeStatus;
    }
}
