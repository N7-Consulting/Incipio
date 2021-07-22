<?php
/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Twig;

use App\Entity\Comment\CommentInterface;
use App\Entity\Comment\ThreadInterface;
use App\Service\Comment\Acl\CommentAclInterface;
use App\Service\Comment\Acl\ThreadAclInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extends Twig to provide some helper functions for the CommentBundle.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentExtension extends AbstractExtension
{
    protected $commentAcl;

    protected $threadAcl;

    public function __construct(CommentAclInterface $commentAcl = null, ThreadAclInterface $threadAcl = null)
    {
        $this->commentAcl = $commentAcl;
        $this->threadAcl = $threadAcl;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('fos_comment_can_comment', [$this, 'canComment']),
            new TwigFunction('fos_comment_can_delete_comment', [$this, 'canDeleteComment']),
            new TwigFunction('fos_comment_can_edit_comment', [$this, 'canEditComment']),
            new TwigFunction('fos_comment_can_edit_thread', [$this, 'canEditThread']),
            new TwigFunction('fos_comment_can_comment_thread', [$this, 'canCommentThread']),
        ];
    }

    /*
     * Checks if the current user is able to comment. Checks if they
     * can create root comments if no $comment is provided, otherwise
     * checks if they can reply to a given comment if supplied.
     *
     * @param  CommentInterface|null $comment
     * @return bool                  If the user is able to comment
     */
    public function canComment(CommentInterface $comment = null)
    {
        if (null !== $comment
            && null !== $comment->getThread()
            && !$comment->getThread()->isCommentable()
        ) {
            return false;
        }
        if (null === $this->commentAcl) {
            return true;
        }
        if (null === $comment) {
            return $this->commentAcl->canCreate();
        }

        return $this->commentAcl->canReply($comment);
    }

    /**
     * Checks if the current user is able to delete a comment.
     *
     * @return bool
     */
    public function canDeleteComment(CommentInterface $comment)
    {
        if (null === $this->commentAcl) {
            return false;
        }

        return $this->commentAcl->canDelete($comment);
    }

    /**
     * Checks if the current user is able to edit a comment.
     *
     * @return bool If the user is able to comment
     */
    public function canEditComment(CommentInterface $comment)
    {
        if (!$comment->getThread()->isCommentable()) {
            return false;
        }
        if (null === $this->commentAcl) {
            return false;
        }

        return $this->commentAcl->canEdit($comment);
    }

    /**
     * Checks if the thread can be edited.
     *
     * Will use the specified ACL, or return true otherwise.
     *
     * @return bool
     */
    public function canEditThread(ThreadInterface $thread)
    {
        if (null === $this->threadAcl) {
            return false;
        }

        return $this->threadAcl->canEdit($thread);
    }

    /**
     * Checks if the thread can be commented.
     *
     * @return bool
     */
    public function canCommentThread(ThreadInterface $thread)
    {
        return $thread->isCommentable()
            && (null === $this->commentAcl || $this->commentAcl->canCreate());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fos_comment';
    }
}
