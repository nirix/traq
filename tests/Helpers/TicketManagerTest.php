<?php

namespace Traq\Tests\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Traq\Helpers\TicketManager;
use Traq\Locale;
use Traq\Models\CustomField;
use Traq\Models\Status;
use Traq\Models\Ticket;
use Traq\Tests\Support\FakeUser;

class TicketManagerTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['locale'] = new Locale();
        $_FILES = [];
    }

    protected function tearDown(): void
    {
        $_FILES = [];
    }

    /**
     * A ticket that never touches the database: saving always fails.
     */
    private function ticket(array $data = []): Ticket
    {
        return new class (array_merge([
            'id' => 1,
            'ticket_id' => 1,
            'project_id' => 3,
            'summary' => 'Original',
            'body' => 'Body',
            'type_id' => 2,
            'status_id' => 1,
        ], $data), false) extends Ticket {
            public function save()
            {
                return false;
            }
        };
    }

    private function manager(Ticket $ticket): TicketManager
    {
        return new TicketManager($ticket, new FakeUser());
    }

    private function customField(array $data): CustomField
    {
        return new CustomField(array_merge([
            'id' => 1,
            'name' => 'Browser',
            'type' => 'text',
            'is_required' => 0,
            'min_length' => 0,
            'max_length' => 0,
            'regex' => '',
            'ticket_type_ids' => '[0]',
        ], $data), false);
    }

    public static function statusTransitions(): array
    {
        $open = Status::STATUS_OPEN;
        $started = Status::STATUS_STARTED;
        $closed = Status::STATUS_CLOSED;

        return [
            'closed to open' => [$closed, $open, ['reopen', 'ticket_reopened']],
            'closed to started' => [$closed, $started, ['reopen', 'ticket_reopened']],
            'open to started' => [$open, $started, ['start', 'ticket_started']],
            'open to closed' => [$open, $closed, ['close', 'ticket_closed']],
            'started to closed' => [$started, $closed, ['close', 'ticket_closed']],
            'started to open' => [$started, $open, [null, 'ticket_updated']],
        ];
    }

    #[DataProvider('statusTransitions')]
    public function testStatusTransition(int $from, int $to, array $expected): void
    {
        $method = new \ReflectionMethod(TicketManager::class, 'statusTransition');

        $this->assertSame($expected, $method->invoke(null, new Status(['status' => $from]), new Status(['status' => $to])));
    }

    public function testUpdateRejectsUnknownFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->manager($this->ticket())->update(['password' => 'hunter2']);
    }

    public function testUpdateAppliesChangesBeforeSaving(): void
    {
        $ticket = $this->ticket();

        $this->assertFalse($this->manager($ticket)->update(['summary' => 'Renamed']));
        $this->assertSame('Renamed', $ticket->summary);
    }

    public function testUpdateRejectsClearingRequiredReferences(): void
    {
        $ticket = $this->ticket();

        $this->manager($ticket)->update(['type_id' => 0, 'status_id' => '']);

        $this->assertArrayHasKey('type', $ticket->errors);
        $this->assertArrayHasKey('status', $ticket->errors);
        $this->assertSame(2, $ticket->type_id);
    }

    public function testUpdateLeavesUnchangedReferencesAlone(): void
    {
        $ticket = $this->ticket();

        $this->manager($ticket)->update(['type_id' => '2']);

        $this->assertSame([], $ticket->errors);
    }

    public function testCustomFieldValueMustBeValid(): void
    {
        $ticket = $this->ticket();

        $this->manager($ticket)->setCustomFields(
            [$this->customField(['id' => 5, 'type' => 'integer'])],
            [5 => 'not a number']
        );

        $this->assertArrayHasKey(5, $ticket->errors);
    }

    public function testRequiredCustomFieldMustBeSubmitted(): void
    {
        $ticket = $this->ticket();

        $this->manager($ticket)->setCustomFields(
            [$this->customField(['id' => 5, 'is_required' => 1])],
            []
        );

        $this->assertArrayHasKey(5, $ticket->errors);
    }

    public function testValidCustomFieldValueIsAccepted(): void
    {
        $ticket = $this->ticket();

        $this->manager($ticket)->setCustomFields(
            [$this->customField(['id' => 5, 'is_required' => 1])],
            [5 => 'Firefox']
        );

        $this->assertSame([], $ticket->errors);
    }

    public function testCustomFieldsForOtherTicketTypesAreSkipped(): void
    {
        $ticket = $this->ticket(['type_id' => 2]);

        $this->manager($ticket)->setCustomFields(
            [$this->customField(['id' => 5, 'is_required' => 1, 'ticket_type_ids' => '[9]'])],
            []
        );

        $this->assertSame([], $ticket->errors);
    }

    public function testCustomFieldsForTheTicketTypeAreChecked(): void
    {
        $ticket = $this->ticket(['type_id' => 2]);

        $this->manager($ticket)->setCustomFields(
            [$this->customField(['id' => 5, 'is_required' => 1, 'ticket_type_ids' => '[2]'])],
            []
        );

        $this->assertArrayHasKey(5, $ticket->errors);
    }

    private function uploadedAttachment(Ticket $ticket): ?array
    {
        return (new \ReflectionMethod(TicketManager::class, 'uploadedAttachment'))->invoke($this->manager($ticket));
    }

    public function testNoUploadedAttachment(): void
    {
        $ticket = $this->ticket();

        $this->assertNull($this->uploadedAttachment($ticket));

        $_FILES['attachment'] = ['name' => '', 'tmp_name' => '', 'error' => UPLOAD_ERR_NO_FILE, 'size' => 0];
        $this->assertNull($this->uploadedAttachment($ticket));

        $this->assertSame([], $ticket->errors);
    }

    public function testFailedUploadAddsAnError(): void
    {
        $ticket = $this->ticket();
        $_FILES['attachment'] = ['name' => 'big.zip', 'tmp_name' => '', 'error' => UPLOAD_ERR_INI_SIZE, 'size' => 0];

        $this->assertNull($this->uploadedAttachment($ticket));
        $this->assertArrayHasKey('attachment', $ticket->errors);
    }

    public function testFileThatWasNotUploadedIsRejected(): void
    {
        $ticket = $this->ticket();
        $path = tempnam(sys_get_temp_dir(), 'traq');
        file_put_contents($path, 'contents');

        $_FILES['attachment'] = ['name' => 'a.txt', 'tmp_name' => $path, 'error' => UPLOAD_ERR_OK, 'size' => 8];

        try {
            $this->assertNull($this->uploadedAttachment($ticket));
            $this->assertArrayHasKey('attachment', $ticket->errors);
        } finally {
            unlink($path);
        }
    }
}
