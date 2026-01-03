<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Application\Constants\Messages;
use App\Domain\DomainException\DomainRecordNotFoundException;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public $message = Messages::USER_NOT_FOUND;
}
