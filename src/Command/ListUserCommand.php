<?php

namespace App\Command;

use App\Dto\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ListUserCommand extends Command
{
    protected static $defaultName = 'user:list';
    protected static $defaultDescription = 'List of users';

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('groupId', InputArgument::OPTIONAL, 'User ID');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];
        if ($input->hasArgument('groupId')) {
            $options['query']['groupId'] = $input->getArgument('groupId');
        }

        try {
            $response = $this->client->request('GET', 'http://server/api/user/', $options);

            foreach ($response->toArray(false) as $userArray) {
                $output->writeln(User::createFromArray($userArray));
            }
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage()); //в реальности логировал бы эксепшн

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
