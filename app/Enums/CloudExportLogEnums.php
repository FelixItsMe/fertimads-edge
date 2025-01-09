<?php

namespace App\Enums;

enum CloudExportLogEnums: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';

    public function getLabelText()
    {
        return match ($this) {
            self::SUCCESS => 'Berhasil',
            self::FAILED => 'Gagal',
        };
    }
}
