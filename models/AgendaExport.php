<?php

namespace MoonWalkerz\Contact\Models;

class AgendaExport extends \Backend\Models\ExportModel
{
    public function exportData($columns, $sessionKey = null)
    {
        $records = Agenda::all();
        $records->each(function ($record) use ($columns) {
            $record->makeVisible($columns);
        });

        return $records->toArray();
    }
}
