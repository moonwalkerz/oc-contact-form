<?php

namespace MoonWalkerz\Contact\Models;

class AgendaImport extends \Backend\Models\ImportModel
{
    public $rules = [
        'email' => 'email',
    ];

    public function set(&$record, $key, $data)
    {
        if (array_key_exists($key, $data)) {
            $record->{$key} = $data[$key];
        }
    }

    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {
            try {
                if (!empty($data['email'])) {
                    $existing = Agenda::where('email', $data['email'])->first();
                    if ($existing) {
                        $this->logSkipped($row, 'Duplicate email: ' . $data['email']);
                        continue;
                    }
                }

                $record = new Agenda;

                $this->set($record, 'name', $data);
                $this->set($record, 'email', $data);
                $this->set($record, 'phone', $data);
                $this->set($record, 'message', $data);
                $this->set($record, 'address', $data);
                $this->set($record, 'city', $data);
                $this->set($record, 'zip', $data);
                $this->set($record, 'state', $data);
                $this->set($record, 'country', $data);

                if (isset($data['sw_gdpr'])) {
                    $record->sw_gdpr = $this->parseBoolean($data['sw_gdpr']);
                }
                if (isset($data['sw_contact'])) {
                    $record->sw_contact = $this->parseBoolean($data['sw_contact']);
                }
                if (isset($data['sw_promo'])) {
                    $record->sw_promo = $this->parseBoolean($data['sw_promo']);
                }
                if (isset($data['sw_third_parties'])) {
                    $record->sw_third_parties = $this->parseBoolean($data['sw_third_parties']);
                }

                $record->save();
                $this->logCreated();
            } catch (\Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }

    protected function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        $trueValues = ['1', 'true', 'yes', 'on', 'y'];
        return in_array(strtolower(trim($value)), $trueValues);
    }
}
