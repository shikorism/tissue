<?php

namespace App;

enum ModerationAction: string
{
    case SuspendCheckin = 'suspend_checkin';
    case SuspendUser = 'suspend_user';
}
