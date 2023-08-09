<?php

namespace App\Security\Voter;

use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const EDIT = 'CAN_EDIT';
    public const VIEW = 'POST_VIEW';

    public function __construct(protected CategoryRepository $categoryRepository)
    {
        
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT]) 
            && $subject instanceof \App\Entity\Category; //, self::VIEW
        // return in_array($attribute, [self::EDIT]) 
        //      && is_numeric($subject); 
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // $category = $this->categoryRepository->find($subject);

        // if(!$category){
        //     return false;
        // }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $subject->getOwner() === $user;
                // break;
            // case self::VIEW:
            //     // logic to determine if the user can VIEW
            //     // return true or false
            //     break;
        }

        return false;
    }
}
