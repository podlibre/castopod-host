<?php

declare(strict_types=1);

/**
 * @copyright  2021 Podlibre
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 * @link       https://castopod.org/
 */

namespace ActivityPub\Entities;

use CodeIgniter\I18n\Time;
use Michalsn\Uuid\UuidEntity;
use RuntimeException;

/**
 * @property string $id
 * @property string $uri
 * @property int $actor_id
 * @property Actor $actor
 * @property string|null $in_reply_to_id
 * @property Note|null $reply_to_note
 * @property string|null $reblog_of_id
 * @property Note|null $reblog_of_note
 * @property string $message
 * @property string $message_html
 * @property int $favourites_count
 * @property int $reblogs_count
 * @property int $replies_count
 * @property Time $published_at
 * @property Time $created_at
 *
 * @property bool $has_preview_card
 * @property PreviewCard|null $preview_card
 *
 * @property bool $has_replies
 * @property Note[] $replies
 * @property Note[] $reblogs
 */
class Note extends UuidEntity
{
    protected ?Actor $actor = null;

    protected ?Note $reply_to_note = null;

    protected ?Note $reblog_of_note = null;

    protected ?PreviewCard $preview_card = null;

    protected bool $has_preview_card = false;

    /**
     * @var Note[]|null
     */
    protected ?array $replies = null;

    protected bool $has_replies = false;

    /**
     * @var Note[]|null
     */
    protected ?array $reblogs = null;

    /**
     * @var string[]
     */
    protected $uuids = ['id', 'in_reply_to_id', 'reblog_of_id'];

    /**
     * @var string[]
     */
    protected $dates = ['published_at', 'created_at'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'uri' => 'string',
        'actor_id' => 'integer',
        'in_reply_to_id' => '?string',
        'reblog_of_id' => '?string',
        'message' => 'string',
        'message_html' => 'string',
        'favourites_count' => 'integer',
        'reblogs_count' => 'integer',
        'replies_count' => 'integer',
    ];

    /**
     * Returns the note's actor
     */
    public function getActor(): Actor
    {
        if ($this->actor_id === null) {
            throw new RuntimeException('Note must have an actor_id before getting actor.');
        }

        if ($this->actor === null) {
            $this->actor = model('ActorModel', false)
                ->getActorById($this->actor_id);
        }

        return $this->actor;
    }

    public function getPreviewCard(): ?PreviewCard
    {
        if ($this->id === null) {
            throw new RuntimeException('Note must be created before getting preview_card.');
        }

        if ($this->preview_card === null) {
            $this->preview_card = model('PreviewCardModel', false)
                ->getNotePreviewCard($this->id);
        }

        return $this->preview_card;
    }

    public function getHasPreviewCard(): bool
    {
        return $this->getPreviewCard() !== null;
    }

    /**
     * @return Note[]
     */
    public function getReplies(): array
    {
        if ($this->id === null) {
            throw new RuntimeException('Note must be created before getting replies.');
        }

        if ($this->replies === null) {
            $this->replies = (array) model('NoteModel', false)
                ->getNoteReplies($this->id);
        }

        return $this->replies;
    }

    public function getHasReplies(): bool
    {
        return $this->getReplies() !== null;
    }

    public function getReplyToNote(): ?self
    {
        if ($this->in_reply_to_id === null) {
            throw new RuntimeException('Note is not a reply.');
        }

        if ($this->reply_to_note === null) {
            $this->reply_to_note = model('NoteModel', false)
                ->getNoteById($this->in_reply_to_id);
        }

        return $this->reply_to_note;
    }

    /**
     * @return Note[]
     */
    public function getReblogs(): array
    {
        if ($this->id === null) {
            throw new RuntimeException('Note must be created before getting reblogs.');
        }

        if ($this->reblogs === null) {
            $this->reblogs = (array) model('NoteModel', false)
                ->getNoteReblogs($this->id);
        }

        return $this->reblogs;
    }

    public function getReblogOfNote(): ?self
    {
        if ($this->reblog_of_id === null) {
            throw new RuntimeException('Note is not a reblog.');
        }

        if ($this->reblog_of_note === null) {
            $this->reblog_of_note = model('NoteModel', false)
                ->getNoteById($this->reblog_of_id);
        }

        return $this->reblog_of_note;
    }

    public function setMessage(string $message): static
    {
        helper('activitypub');

        $messageWithoutTags = strip_tags($message);

        $this->attributes['message'] = $messageWithoutTags;
        $this->attributes['message_html'] = str_replace("\n", '<br />', linkify($messageWithoutTags));

        return $this;
    }
}
