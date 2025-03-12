<?php

namespace App\Enum;

enum PermissionEnum: string
{
    // Event permissions
    case EVENT_LIST = 'event-list';
    case EVENT_VIEW = 'event-view';
    case EVENT_JOIN = 'event-join';
    case EVENT_CREATE = 'event-create';
    case EVENT_EDIT = 'event-edit';
    case EVENT_DELETE = 'event-delete';

    // User permissions
    case USER_LIST = 'user-list';
    case USER_VIEW = 'user-view';
    case USER_CREATE = 'user-create';
    case USER_EDIT = 'user-edit';
    case USER_DELETE = 'user-delete';

    /**
     * Return all permission values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
