<?php

namespace App\Enums;

enum NotificationChannelEnum: string
{
    case SLACK = 'slack';
    case EMAIL = 'email';
}
