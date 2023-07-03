<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case EMPTY = 'empty';
    case MORE_THAN = 'more_than';
    case LESS_THAN = 'less_than';
}
