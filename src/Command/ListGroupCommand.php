<?php

namespace App\Command;

use App\Dto\Group;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ListGroupCommand extends Command
{
    protected static $defaultName = 'group:list';
    protected static $defaultDescription = 'List of groups';

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $response = $this->client->request(
                'GET',
                'http://server/api/group/',
                [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                ]
            );

            foreach ($response->toArray(false) as $userArray) {
                $output->writeln(Group::createFromArray($userArray));
            }
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage()); //в реальности логировал бы эксепшн

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
