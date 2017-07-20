<?php
declare(strict_types=1);

namespace Teamo\Project\Presentation\Http\Controller;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Teamo\Common\Application\CommandBus;
use Teamo\Common\Http\Controller;
use Teamo\Project\Application\Command\Discussion\ArchiveDiscussionCommand;
use Teamo\Project\Application\Command\Discussion\PostDiscussionCommentCommand;
use Teamo\Project\Application\Command\Discussion\RemoveDiscussionCommand;
use Teamo\Project\Application\Command\Discussion\RemoveDiscussionCommentCommand;
use Teamo\Project\Application\Command\Discussion\RestoreDiscussionCommand;
use Teamo\Project\Application\Command\Discussion\StartDiscussionCommand;
use Teamo\Project\Application\Command\Discussion\UpdateDiscussionCommand;
use Teamo\Project\Application\Command\Discussion\UpdateDiscussionCommentCommand;
use Teamo\Project\Domain\Model\Project\Comment\CommentId;
use Teamo\Project\Domain\Model\Project\Discussion\DiscussionCommentRepository;
use Teamo\Project\Domain\Model\Project\Discussion\DiscussionId;
use Teamo\Project\Domain\Model\Project\Discussion\DiscussionRepository;
use Teamo\Project\Domain\Model\Project\ProjectId;
use Teamo\Project\Domain\Model\Project\ProjectRepository;
use Teamo\Project\Domain\Model\Team\TeamMemberId;
use Teamo\Project\Presentation\Http\Request\StartDiscussionRequest;
use Teamo\Project\Presentation\Http\Request\UpdateCommentRequest;
use Teamo\Project\Presentation\Http\Request\UpdateDiscussionRequest;

class DiscussionController extends Controller
{
    public function index(
        string $projectId,
        DiscussionRepository $discussionRepository,
        ProjectRepository $projectRepository
    ){
        return view('project.discussion.index', [
            'project' => $projectRepository->ofId(new ProjectId($projectId), new TeamMemberId($this->authenticatedId())),
            'discussions' => $discussionRepository->allActive(new ProjectId($projectId)),
        ]);
    }

    public function archive(
        string $projectId,
        DiscussionRepository $discussionRepository,
        ProjectRepository $projectRepository
    ){
        return view('project.discussion.archive', [
            'project' => $projectRepository->ofId(new ProjectId($projectId), new TeamMemberId($this->authenticatedId())),
            'discussions' => $discussionRepository->allArchived(new ProjectId($projectId)),
        ]);
    }

    public function show(
        string $projectId,
        string $discussionId,
        DiscussionRepository $discussionRepository,
        DiscussionCommentRepository $commentRepository,
        ProjectRepository $projectRepository
    ) {
        return view('project.discussion.show', [
            'project' => $projectRepository->ofId(new ProjectId($projectId), new TeamMemberId($this->authenticatedId())),
            'discussion' => $discussionRepository->ofId(new DiscussionId($discussionId), new ProjectId($projectId)),
            'comments' => $commentRepository->all(new DiscussionId($discussionId)),
        ]);
    }

    public function create()
    {
        return view('project.discussion.create');
    }

    public function store(string $projectId, StartDiscussionRequest $request, CommandBus $commandBus)
    {
        $discussionId = Uuid::uuid4()->toString();

        $files = [];

        $command = new StartDiscussionCommand($projectId, $discussionId, $this->authenticatedId(), $request->get('topic'), $request->get('content'), $files);
        $commandBus->handle($command);

        return redirect(route('project.discussion.show', [$projectId, $discussionId]));
    }

    public function edit(string $projectId, string $discussionId, DiscussionRepository $discussionRepository)
    {
        return view('project.discussion.edit', [
            'discussion' => $discussionRepository->ofId(new DiscussionId($discussionId), new ProjectId($projectId)),
        ]);
    }

    public function update(string $projectId, string $discussionId, UpdateDiscussionRequest $request, CommandBus $commandBus)
    {
        $files = [];

        $command = new UpdateDiscussionCommand($projectId, $discussionId, $this->authenticatedId(), $request->get('topic'), $request->get('content'), $files);
        $commandBus->handle($command);

        return redirect(route('project.discussion.show', [$projectId, $discussionId]))
            ->with('success', trans('app.flash_discussion_updated'));
    }

    public function archiveDiscussion(string $projectId, string $discussionId, CommandBus $commandBus)
    {
        $command = new ArchiveDiscussionCommand($projectId, $discussionId, $this->authenticatedId());
        $commandBus->handle($command);

        return redirect(route('project.discussion.index', $projectId))->with('success', trans('app.flash_discussion_archived'));
    }

    public function restoreDiscussion(string $projectId, string $discussionId, CommandBus $commandBus)
    {
        $command = new RestoreDiscussionCommand($projectId, $discussionId, $this->authenticatedId());
        $commandBus->handle($command);

        return redirect(route('project.discussion.show', [$projectId, $discussionId]))
            ->with('success', trans('app.flash_discussion_restored'));
    }

    public function destroy(string $projectId, string $discussionId, CommandBus $commandBus)
    {
        $command = new RemoveDiscussionCommand($projectId, $discussionId, $this->authenticatedId());
        $commandBus->handle($command);

        $route = strstr(\URL::previous(), 'archive') ? 'archive' : 'index';

        return redirect(route('project.discussion.' . $route, $projectId))->with('success', trans('app.flash_discussion_deleted'));
    }


    /* Comments */


    public function storeComment(string $projectId, string $discussionId, Request $request, CommandBus $commandBus)
    {
        $commentId = Uuid::uuid4()->toString();

        $command = new PostDiscussionCommentCommand($projectId, $discussionId, $commentId, $this->authenticatedId(), $request->get('content'), []);
        $commandBus->handle($command);

        return redirect(route('project.discussion.show', [$projectId, $discussionId]) . '#comment-' . $commentId);
    }

    public function editComment(string $projectId, string $discussionId, string $commentId, DiscussionCommentRepository $commentRepository)
    {
        return view('project.discussion.edit_comment', [
            'discussionId' => $discussionId,
            'comment' => $commentRepository->ofId(new CommentId($commentId), new DiscussionId($discussionId)),
        ]);
    }

    public function updateComment(string $projectId, string $discussionId, string $commentId, UpdateCommentRequest $request, CommandBus $commandBus)
    {
        $command = new UpdateDiscussionCommentCommand($projectId, $discussionId, $commentId, $this->authenticatedId(), $request->get('content'));
        $commandBus->handle($command);

        return redirect(route('project.discussion.show', [$projectId, $discussionId]) . '#comment-' . $commentId);
    }

    public function ajaxDestroyComment(string $projectId, string $discussionId, string $commentId, CommandBus $commandBus)
    {
        try {
            $command = new RemoveDiscussionCommentCommand($projectId, $discussionId, $commentId, $this->authenticatedId());
            $commandBus->handle($command);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
