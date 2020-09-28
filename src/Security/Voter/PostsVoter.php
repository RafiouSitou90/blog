<?php

namespace App\Security\Voter;

use App\Entity\Posts;
use App\Entity\Users;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PostsVoter
 * @package App\Security\Voter
 */
class PostsVoter extends Voter
{
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const SHOW = 'show';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT, self::SHOW])
            && $subject instanceof Posts;
    }

    /**
     * @param string $attribute
     * @param Posts $post
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $post, TokenInterface $token): bool
    {
        /** @var Users $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
            case self::SHOW:
                 $vote = $user === $post->getAuthor();
                break;
            default:
                $vote = false;
        }

        return $vote;
    }
}
