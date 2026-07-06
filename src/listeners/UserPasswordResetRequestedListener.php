<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\listeners;

use Besnovatyj\User\entities\events\UserPasswordResetRequested;
use RuntimeException;
use yii\mail\MailerInterface;

class UserPasswordResetRequestedListener
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(UserPasswordResetRequested $event): void
    {
        $sent = $this->mailer
            ->compose(
                ['html' => 'user/auth/reset/request-html', 'text' => 'user/auth/reset/request-text'],
                ['user' => $event->user]
            )
            ->setTo($event->user->email)
            ->setSubject('Запрос на восстановление пароля')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Ошибка отправки E-mail.');
        }
    }
}
