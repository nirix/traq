<?php

namespace Traq\Tests\Helpers;

use Avalon\Http\Request;
use PHPUnit\Framework\TestCase;
use Traq\Helpers\TicketInput;
use Traq\Models\Project;
use Traq\Tests\Support\FakeUser;

class TicketInputTest extends TestCase
{
    protected function setUp(): void
    {
        Request::$request = [];
        Request::$post = [];
    }

    protected function tearDown(): void
    {
        Request::$request = [];
        Request::$post = [];
    }

    /**
     * @param string[] $permissions
     */
    private function input(array $permissions = []): TicketInput
    {
        return new TicketInput(new FakeUser(7, $permissions), new Project(['id' => 3]));
    }

    public function testChangesOnlyIncludesPermittedProperties(): void
    {
        Request::$post = ['summary' => 'New summary', 'status' => '2', 'priority' => '1'];

        $changes = $this->input(['ticket_properties_change_summary', 'ticket_properties_change_status'])->changes();

        $this->assertEquals(['summary' => 'New summary', 'status_id' => '2'], $changes);
    }

    public function testChangesLeavesOutMissingAndNonScalarValues(): void
    {
        Request::$post = ['summary' => ['nope'], 'status' => '2'];

        $changes = $this->input([
            'ticket_properties_change_summary',
            'ticket_properties_change_status',
            'ticket_properties_change_type',
        ])->changes();

        $this->assertSame(['status_id' => '2'], $changes);
    }

    public function testChangesTrimsText(): void
    {
        Request::$post = ['summary' => "  padded \n"];

        $this->assertSame(
            ['summary' => 'padded'],
            $this->input(['ticket_properties_change_summary'])->changes()
        );
    }

    public function testTimeFieldsUseTheirOwnPermission(): void
    {
        Request::$post = ['time_proposed' => '2h', 'time_worked' => '1h'];

        $this->assertSame(
            ['time_proposed' => '2h'],
            $this->input(['ticket_properties_change_time_proposed'])->changes()
        );

        $this->assertSame(
            ['time_worked' => '1h'],
            $this->input(['ticket_properties_change_time_worked'])->changes()
        );
    }

    public function testChangesDropsBlankAndMalformedTasks(): void
    {
        Request::$post = ['tasks' => json_encode([
            ['task' => 'Write tests', 'completed' => true, 'extra' => 'ignored'],
            ['task' => '   '],
            ['task' => ['not a string']],
            'not an array',
            ['task' => 'Ship it'],
        ])];

        $changes = $this->input(['ticket_properties_change_tasks'])->changes();

        $this->assertSame([
            ['task' => 'Write tests', 'completed' => true],
            ['task' => 'Ship it', 'completed' => false],
        ], $changes['tasks']);
    }

    public function testChangesIgnoresTasksThatAreNotAList(): void
    {
        Request::$post = ['tasks' => 'not json'];

        $this->assertSame([], $this->input(['ticket_properties_change_tasks'])->changes());
    }

    public function testChangesIgnoresTasksWithoutPermission(): void
    {
        Request::$post = ['tasks' => json_encode([['task' => 'Nope']])];

        $this->assertSame([], $this->input()->changes());
    }

    public function testBulkChangesSkipsUnchangedValues(): void
    {
        Request::$post = ['status' => '-1', 'version' => '0', 'milestone' => '4', 'summary' => 'Ignored'];

        $changes = $this->input([
            'ticket_properties_change_status',
            'ticket_properties_change_version',
            'ticket_properties_change_milestone',
            'ticket_properties_change_summary',
        ])->bulkChanges();

        $this->assertEquals(['version_id' => '0', 'milestone_id' => '4'], $changes);
    }

    public function testCommentRequiresPermission(): void
    {
        Request::$post = ['comment' => 'Looks good'];

        $this->assertNull($this->input()->comment());
        $this->assertSame('Looks good', $this->input(['comment_on_tickets'])->comment());
    }

    public function testCommentMustBeAString(): void
    {
        Request::$post = ['comment' => ['nope']];

        $this->assertNull($this->input(['comment_on_tickets'])->comment());
    }

    public function testCustomFields(): void
    {
        $this->assertSame([], $this->input()->customFields());

        Request::$post = ['custom_fields' => 'nope'];
        $this->assertSame([], $this->input()->customFields());

        Request::$post = ['custom_fields' => [4 => 'value']];
        $this->assertSame([4 => 'value'], $this->input()->customFields());
    }

    public function testNewTicketDefaults(): void
    {
        Request::$post = ['summary' => ' A bug ', 'description' => 'It broke'];

        $this->assertSame([
            'summary'      => 'A bug',
            'body'         => 'It broke',
            'user_id'      => 7,
            'project_id'   => 3,
            'type_id'      => 1,
            'severity_id'  => 4,
            'milestone_id' => null,
            'version_id'   => null,
            'component_id' => null,
            'tasks'        => [],
            'is_private'   => 0,
        ], $this->input()->newTicket());
    }

    public function testNewTicketOnlyUsesPropertiesTheUserMaySet(): void
    {
        Request::$post = [
            'type' => '2',
            'milestone' => '5',
            'version' => '0',
            'severity' => '2',
            'priority' => '1',
            'assigned_to' => '9',
            'time_proposed' => '3h',
            'time_worked' => '1h',
        ];

        $data = $this->input([
            'ticket_properties_set_milestone',
            'ticket_properties_set_version',
            'ticket_properties_set_severity',
            'ticket_properties_set_time_proposed',
        ])->newTicket();

        $this->assertSame(2, $data['type_id']);
        $this->assertSame(5, $data['milestone_id']);
        $this->assertNull($data['version_id']);
        $this->assertSame(2, $data['severity_id']);
        $this->assertSame('3h', $data['time_proposed']);
        $this->assertArrayNotHasKey('priority_id', $data);
        $this->assertArrayNotHasKey('assigned_to_id', $data);
        $this->assertArrayNotHasKey('time_worked', $data);
    }

    public function testNewTicketTasks(): void
    {
        Request::$post = ['tasks' => json_encode([['task' => 'First']])];

        $this->assertSame([], $this->input()->newTicket()['tasks']);
        $this->assertSame(
            [['task' => 'First', 'completed' => false]],
            $this->input(['ticket_properties_set_tasks'])->newTicket()['tasks']
        );
    }
}
