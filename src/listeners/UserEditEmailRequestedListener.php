<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\listeners;

use Besnovatyj\User\entities\events\UserEditEmailRequested;
use yii\mail\MailerInterface;

class UserEditEmailRequestedListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserEditEmailRequested $event): void
    {
        $sent = $this->mailer
            ->compose(
                ['html' => 'User/auth/editEmail/request-html', 'text' => 'User/auth/editEmail/request-text'],
                ['user' => $event->user]
            )
            ->setTo($event->user->new_email)
            ->setSubject('Подтверждение E-mail')
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки E-mail.');
        }
    }
}
