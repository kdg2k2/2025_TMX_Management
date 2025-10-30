<?php

namespace App\Traits;

trait FormatDateTraits
{
    public function formatDateTimeForPreview($time)
    {
        if (empty($time))
            return null;
        return date('d/m/Y H:i:s', strtotime($time));
    }

    public function formatDateForPreview($date)
    {
        if (empty($date))
            return null;
        return date('d/m/Y', strtotime($date));
    }
}
