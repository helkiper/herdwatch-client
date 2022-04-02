<?php

namespace App\Command;

use App\Dto\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdateUserCommand extends Command
{
    protected static $defaultName = 'user:update';
    protected static $defaultDescription = 'Update user';

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
            ->addArgument('id', InputArgument::REQUIRED, 'User ID')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'New user name')
            ->addOption('email', null,InputArgument::OPTIONAL, 'New user email');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requestData = [];
        $input->getOption('name') && $requestData['name'] = $input->getOption('name');
        $input->getOption('email') && $requestData['email'] = $input->getOption('email');

        try {
            $response = $this->client->request(
                'PUT',
                sprintf('http://server/api/user/%d', $input->getArgument('id')),
                [
                    'headers' => [
                        'accept' => 'application/json',
                    ],
                    'json' => $requestData
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
