<?php
declare(strict_types=1);

namespace Tests\Unit\Project\Domain\Model\Project;

use Illuminate\Support\Collection;
use Teamo\Project\Domain\Model\Project\Comment\CommentId;
use Teamo\Project\Domain\Model\Project\Event\Event;
use Teamo\Project\Domain\Model\Project\Event\EventComment;
use Teamo\Project\Domain\Model\Project\Event\EventId;
use Teamo\Project\Domain\Model\Project\ProjectId;
use Teamo\Project\Domain\Model\Team\TeamMemberId;
use Tests\TestCase;

class EventTest extends TestCase
{
    /**
     * @var Event
     */
    private $event;

    public function setUp()
    {
        $this->event = new Event(
            new ProjectId('id-1'),
            new EventId('id-1'),
            new TeamMemberId('id-1'),
            'My Event',
            'Event Details',
            new \DateTimeImmutable()
        );
    }

    public function testEventCanBeCommented()
    {
        $author = new TeamMemberId('id-1');

        $comment = $this->event->comment(new CommentId('1'), $author, 'Comment content', new Collection());

        $this->assertInstanceOf(EventComment::class, $comment);
    }

    public function testEventCanBeUpdated()
    {
        $this->assertEquals('My Event', $this->event->name());
        $this->assertEquals('Event Details', $this->event->details());

        $occursOn = new \DateTimeImmutable();
        $this->event->update('New Event', 'New Details', $occursOn);
        $this->assertEquals('New Event', $this->event->name());
        $this->assertEquals('New Details', $this->event->details());
        $this->assertEquals($occursOn, $this->event->occursOn());
    }

    public function testEventCanBeArchivedAndRestored()
    {
        $this->assertFalse($this->event->isArchived());

        $this->event->archive();
        $this->assertTrue($this->event->isArchived());

        $this->event->restore();
        $this->assertFalse($this->event->isArchived());
    }
}
