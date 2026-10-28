<?php

namespace Traq\Tests\Support;

use Traq\Models\User;

/**
 * A user whose project permissions are given up front, so no database is needed.
 */
class FakeUser extends User
{
    /**
     * @param string[] $permissions
     */
    public function __construct(int $id = 7, private array $allowed = [], private int $projectId = 3)
    {
        parent::__construct(['id' => $id]);
    }

    public function permission($project_id, $action)
    {
        return $project_id == $this->projectId && in_array($action, $this->allowed, true);
    }
}
