<?php

namespace App\Dto;

class User
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
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $groups;

    public static function createFromArray(array $data): self
    {
        $instance = new static();

        isset($data['id']) && $instance->id = $data['id'];
        isset($data['name']) && $instance->name = $data['name'];
        isset($data['email']) && $instance->email = $data['email'];

        $instance->groups = isset($data['groups']) ? array_map(function (array $group) {
            return Group::createFromArray($group);
        }, $data['groups']) : [];

        return $instance;
    }

    public function __toString(): string
    {
        $result = 'User: ' . PHP_EOL;

        $this->id && $result .= "ID - $this->id" . PHP_EOL;
        $this->name && $result .= "name - $this->name" . PHP_EOL;
        $this->email && $result .= "email - $this->email" . PHP_EOL;

        if (!empty($this->groups)) {
            foreach ($this->groups as $group) {
                $result .= $group;
            }
        }

        return $result;
    }
}
