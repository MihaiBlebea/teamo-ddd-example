<?php
declare(strict_types=1);

namespace Teamo\Project\Application\Command\TodoList;

class AddTodoCommand
{
    private $projectId;
    private $todoListId;
    private $todoId;
    private $creator;
    private $name;
    private $assignee;
    private $deadline;

    public function __construct(string $projectId, string $todoListId, string $todoId, string $creator, string $name, string $assignee, string $deadline)
    {
        $this->projectId = $projectId;
        $this->todoListId = $todoListId;
        $this->todoId = $todoId;
        $this->creator = $creator;
        $this->name = $name;
        $this->assignee = $assignee;
        $this->deadline = $deadline;
    }

    public function projectId(): string
    {
        return $this->projectId;
    }

    public function todoListId(): string
    {
        return $this->todoListId;
    }

    public function todoId(): string
    {
        return $this->todoId;
    }

    public function creator(): string
    {
        return $this->creator;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function assignee(): string
    {
        return $this->assignee;
    }

    public function deadline(): string
    {
        return $this->deadline;
    }
}
