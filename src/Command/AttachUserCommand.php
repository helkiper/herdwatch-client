<?php

namespace App\Command;

use App\Dto\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AttachUserCommand extends Command
{
    protected static $defaultName = 'user:attach';
    protected static $defaultDescription = 'Attach user to group';

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
            ->addArgument('userId', InputArgument::REQUIRED, 'User ID')
            ->addArgument('groupId', InputArgument::REQUIRED, 'Group ID');
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
                'PATCH',
                sprintf(
                    'http://server/api/user/attach/%d/%d',
                    $input->getArgument('userId'),
                    $input->getArgument('groupId')
                ),
                [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            $responseArray = $response->toArray(false);
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage()); //в реальности логировал бы эксепшн

            return Command::FAILURE;
        }

        $output->writeln(
            $statusCode == '200'
                ? User::createFromArray($responseArray)
                : sprintf('%d %s', $statusCode, $responseArray['message'])
        );

        return Command::SUCCESS;
    }
}
