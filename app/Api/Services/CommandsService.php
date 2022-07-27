<?php

namespace Api\Services;

use Api\Repositories\DeviceRepository;
use Api\Validators\CommandsValidator;

/**
 * Class CommandsService
 * @package Api\Services
 */
class CommandsService
{
    /**
     * @var CommandsValidator
     */
    protected $validator;

    /**
     * @var DeviceRepository
     */
    protected $deviceRepository;

    /**
     * CommandsService constructor.
     * @param CommandsValidator $commandsValidator
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(
        CommandsValidator $commandsValidator,
        DeviceRepository $deviceRepository
    )
    {
        $this->validator = $commandsValidator;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function command(array $data): array
    {
        $this->validator
            ->setData($data)
            ->validate('command');

        $connectedClients = $this->deviceRepository
            ->resetCriteria()
            ->withActive()
            ->count();

        return [
            'status_code' => \ConstBoolean::TRUE,
            'database_server' => \ConstBoolean::TRUE,
            'connected_clients' => $connectedClients,
            'echo_command' => $data['action'],
        ];
    }
}
