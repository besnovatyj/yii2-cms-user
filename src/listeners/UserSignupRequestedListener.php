<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\listeners;

use Besnovatyj\User\entities\events\UserSignUpRequested;
use yii\mail\MailerInterface;

class UserSignupRequestedListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserSignUpRequested $event): void
    {
        $sent = $this->mailer
            ->compose(
                ['html' => 'user/auth/signup/request-html', 'text' => 'user/auth/signup/request-text'],
                ['user' => $event->user]
            )
            ->setTo($event->user->email)
            ->setSubject('Подтверждение регистрации')
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки E-mail.');
        }
    }
}
