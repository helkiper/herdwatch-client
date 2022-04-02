<?php

namespace App\Dto;

class Group
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $users;

    public static function createFromArray(array $data): self
    {
        $instance = new static();

        isset($data['id']) && $instance->id = $data['id'];
        isset($data['name']) && $instance->name = $data['name'];

        $instance->users = isset($data['users']) ? array_map(function (array $user) {
            return User::createFromArray($user);
        }, $data['users']) : [];

        return $instance;
    }

    public function __toString(): string
    {
        $result = 'Group: '.PHP_EOL;

        $this->id && $result .= "ID - $this->id".PHP_EOL;
        $this->name && $result .= "name - $this->name".PHP_EOL;

        if (!empty($this->users)) {
            foreach ($this->users as $user) {
                $result .= $user;
            }
        }

        return $result;
    }
}
