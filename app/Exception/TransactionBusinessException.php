<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\TransactionErrorCode;
use Throwable;

/**
 * 事务业务异常
 */
class TransactionBusinessException extends BusinessException
{
    public function __construct(int $code = 0, ?string $message = null, ?Throwable $previous = null)
    {
        if ($message === null) {
            $message = TransactionErrorCode::getMessage($code);
        }

        parent::__construct($code, $message, $previous);
    }
}