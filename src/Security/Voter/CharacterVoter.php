<?php

namespace App\Security\Voter;

use App\Entity\Character;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote; // Importation nécessaire
use Symfony\Component\Security\Core\User\UserInterface;

class CharacterVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Vérifie si l'attribut est supporté et si le sujet est une instance de Character
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Character;
    }

    /**
     * @param string $attribute
     * @param Character $subject
     * @param TokenInterface $token
     * @param Vote|null $vote  <-- Correction ici : utilisation du type Vote au lieu de int
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté, on refuse l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Character $character */
        $character = $subject;

        // Logique d'autorisation : l'utilisateur doit être le propriétaire du personnage
        return $character->getUser() === $user;
    }
}
