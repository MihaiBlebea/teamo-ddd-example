<?php
declare(strict_types=1);

namespace Teamo\Project\Application\Command\Discussion;

class PostDiscussionCommentCommand
{
    private $projectId;
    private $discussionId;
    private $author;
    private $commentId;
    private $content;
    private $attachments;

    public function __construct(string $projectId, string $discussionId, string $commentId, string $author, string $content, array $attachments)
    {
        $this->projectId = $projectId;
        $this->discussionId = $discussionId;
        $this->commentId = $commentId;
        $this->author = $author;
        $this->content = $content;
        $this->attachments = $attachments;
    }

    public function projectId(): string
    {
        return $this->projectId;
    }

    public function discussionId(): string
    {
        return $this->discussionId;
    }

    public function commentId(): string
    {
        return $this->commentId;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function attachments(): array
    {
        return $this->attachments;
    }
}
