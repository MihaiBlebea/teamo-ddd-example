<?php
declare(strict_types=1);

namespace Teamo\User\Domain\Model\User;

use Teamo\Common\Domain\Entity;

class User extends Entity
{
    private $userId;
    private $name;
    private $email;
    private $password;
    private $avatar;
    private $preferences;

    public function __construct(UserId $userId, string $name, string $email, string $password, string $timezone = '')
    {
        $this->setUserId($userId);
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setPreferences(Preferences::default($timezone));
    }

    public function rename(string $name)
    {
        $this->setName($name);
    }

    public function changeEmail(string $email)
    {
        $this->setEmail($email);
    }

    public function changePassword(string $password)
    {
        $this->setPassword($password);
    }

    public function updatePreferences(Preferences $preferences)
    {
        $this->setPreferences($preferences);
    }

    public function updateAvatar(Avatar $avatar)
    {
        $this->setAvatar($avatar);
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function avatar(): Avatar
    {
        return $this->avatar;
    }

    public function preferences(): Preferences
    {
        return $this->preferences;
    }

    private function setUserId(UserId $userId)
    {
        $this->userId = $userId;
    }

    private function setName(string $name)
    {
        $this->name = $name;
    }

    private function setEmail(string $email)
    {
        $this->email = $email;
    }

    private function setPassword(string $password)
    {
        $this->password = $password;
    }

    private function setAvatar(Avatar $avatar)
    {
        $this->avatar = $avatar;
    }

    private function setPreferences(Preferences $preferences)
    {
        $this->preferences = $preferences;
    }
}
