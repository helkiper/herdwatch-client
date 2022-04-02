<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeleteUserCommand extends Command
{
    protected static $defaultName = 'user:delete';
    protected static $defaultDescription = 'Delete user';

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
            ->addArgument('id', InputArgument::REQUIRED, 'User ID');
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
                'DELETE',
                sprintf('http://server/api/user/%d', $input->getArgument('id')),
                [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage()); //в реальности логировал бы эксепшн

            return Command::FAILURE;
        }

        $output->writeln(
            $statusCode == '204'
                ? "User successfully deleted"
                : sprintf('%d %s', $statusCode, $response->toArray(false)['message'])
        );

        return Command::SUCCESS;
    }
}
