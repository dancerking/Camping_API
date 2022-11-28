<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\RateCalendarModel;
use App\Models\RateModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;
class RateCalendar extends APIBaseController
{
    /**
     * Return an array of Rate calendar
     * GET/baseratesettings
     * @return mixed
     */
    use ResponseTrait;

    /**
     * Update a model resource
     * PUT/baseratesettings/update
     * @return mixed
     */
    public function update($id = null)
    {
        $config = config('Config\App');
        /* Load Rate relation Models */
        $rate_calendar_model = new RateCalendarModel();
        $rate_model = new RateModel();
        $type_mapping_model = new TypeMappingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'rate_code' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'rate_calendar'
            );
        }

        /* Getting request data */
        $rate_code = $this->request->getVar('rate_code');
        if (!is_array($rate_code)) {
            return $this->notifyError(
                'rate_code should be array',
                'invalid_data',
                'rate_calendar'
            );
        }
        if (
            count($rate_code) > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'Max rows are ' .
                    $config->MAXIMUM_DATE_RANGE,
                'overflow',
                'rate_calendar'
            );
        }

        /* Format validation */
        foreach ($rate_code as $row) {
            if (!isset($row->daily_rate_code)) {
                return $this->notifyError(
                    'daily_rate_code is required',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (!isset($row->daily_rate_type)) {
                return $this->notifyError(
                    'daily_rate_type is required',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (!isset($row->daily_rate_from)) {
                return $this->notifyError(
                    'daily_rate_from is required',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (!isset($row->daily_rate_to)) {
                return $this->notifyError(
                    'daily_rate_to is required',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (!isset($row->daily_rate_baserate)) {
                return $this->notifyError(
                    'daily_rate_baserate is required',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (
                !ctype_digit((string) $row->daily_rate_code)
            ) {
                return $this->notifyError(
                    'daily_rate_code format is incorrect',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (
                $rate_model->find($row->daily_rate_code) ==
                null
            ) {
                return $this->notifyError(
                    'No Such daily_rate_code(rate_id)',
                    'notFound',
                    'rate_calendar'
                );
            }
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_code' =>
                            $row->daily_rate_type,
                    ])
                    ->findAll() == null
            ) {
                return $this->notifyError(
                    'No Such daily_rate_type(type_mapping_code).',
                    'notFound',
                    'rate_calendar'
                );
            }
            if (
                !$this->validateDate(
                    $row->daily_rate_from
                ) ||
                !$this->validateDate($row->daily_rate_to)
            ) {
                return $this->notifyError(
                    'Date format is incorrect',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (
                new DateTime() >
                new DateTime($row->daily_rate_from)
            ) {
                return $this->notifyError(
                    'daily_rate_from should be greater than today',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (
                new DateTime($row->daily_rate_to) <
                new DateTime($row->daily_rate_from)
            ) {
                return $this->notifyError(
                    'daily_rate_to should be greater than daily_rate_from.',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (
                date_diff(
                    new DateTime($row->daily_rate_to),
                    new DateTime($row->daily_rate_from)
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days back date range',
                    'invalid_data',
                    'rate_calendar'
                );
            }
            if (!is_numeric($row->daily_rate_baserate)) {
                return $this->notifyError(
                    'daily_rate_baserate format is incorrect'
                );
            }
            if (
                isset($row->daily_rate_guesttype_1) &&
                !empty($row->daily_rate_guesttype_1) &&
                !is_numeric($row->daily_rate_guesttype_1)
            ) {
                return $this->notifyError(
                    'daily_rate_guesttype_1 format is incorrect'
                );
            }
            if (
                isset($row->daily_rate_guesttype_2) &&
                !empty($row->daily_rate_guesttype_2) &&
                !is_numeric($row->daily_rate_guesttype_2)
            ) {
                return $this->notifyError(
                    'daily_rate_guesttype_2 format is incorrect'
                );
            }
            if (
                isset($row->daily_rate_guesttype_3) &&
                !empty($row->daily_rate_guesttype_3) &&
                !is_numeric($row->daily_rate_guesttype_3)
            ) {
                return $this->notifyError(
                    'daily_rate_guesttype_3 format is incorrect'
                );
            }
            if (
                isset($row->daily_rate_guesttype_4) &&
                !empty($row->daily_rate_guesttype_4) &&
                !is_numeric($row->daily_rate_guesttype_4)
            ) {
                return $this->notifyError(
                    'daily_rate_guesttype_4 format is incorrect'
                );
            }
            if (
                isset($row->daily_rate_minstay) &&
                !empty($row->daily_rate_minstay) &&
                !ctype_digit(
                    (string) $row->daily_rate_minstay
                )
            ) {
                return $this->notifyError(
                    'daily_rate_minstay format is incorrect'
                );
            }
            if (
                isset($row->daily_rate_maxstay) &&
                !empty($row->daily_rate_maxstay) &&
                !ctype_digit(
                    (string) $row->daily_rate_maxstay
                )
            ) {
                return $this->notifyError(
                    'daily_rate_maxstay format is incorrect'
                );
            }
        }
        /* Update data in DB */
        foreach ($rate_code as $row) {
            if (
                !isset($row->daily_rate_guesttype_1) ||
                empty($row->daily_rate_guesttype_1)
            ) {
                $daily_rate_guesttype_1 = 0;
            } else {
                $daily_rate_guesttype_1 =
                    $row->daily_rate_guesttype_1;
            }
            if (
                !isset($row->daily_rate_guesttype_2) ||
                empty($row->daily_rate_guesttype_2)
            ) {
                $daily_rate_guesttype_2 = 0;
            } else {
                $daily_rate_guesttype_2 =
                    $row->daily_rate_guesttype_2;
            }
            if (
                !isset($row->daily_rate_guesttype_3) ||
                empty($row->daily_rate_guesttype_3)
            ) {
                $daily_rate_guesttype_3 = 0;
            } else {
                $daily_rate_guesttype_3 =
                    $row->daily_rate_guesttype_3;
            }
            if (
                !isset($row->daily_rate_guesttype_4) ||
                empty($row->daily_rate_guesttype_4)
            ) {
                $daily_rate_guesttype_4 = 0;
            } else {
                $daily_rate_guesttype_4 =
                    $row->daily_rate_guesttype_4;
            }
            if (
                !isset($row->daily_rate_minstay) ||
                empty($row->daily_rate_minstay)
            ) {
                $daily_rate_minstay = 0;
            } else {
                $daily_rate_minstay =
                    $row->daily_rate_minstay;
            }
            if (
                !isset($row->daily_rate_maxstay) ||
                empty($row->daily_rate_maxstay)
            ) {
                $daily_rate_maxstay = 0;
            } else {
                $daily_rate_maxstay =
                    $row->daily_rate_maxstay;
            }
            $from = $row->daily_rate_from;
            $to = $row->daily_rate_to;
            while (strtotime($from) <= strtotime($to)) {
                $data = [
                    'rate_calendar_host_id' => $host_id,
                    'daily_rate_code' =>
                        $row->daily_rate_code,
                    'daily_rate_type' =>
                        $row->daily_rate_type,
                    'daily_rate_day' => $from,
                    'daily_rate_baserate' =>
                        $row->daily_rate_baserate,
                    'daily_rate_guesttype_1' => $daily_rate_guesttype_1,
                    'daily_rate_guesttype_2' => $daily_rate_guesttype_2,
                    'daily_rate_guesttype_3' => $daily_rate_guesttype_3,
                    'daily_rate_guesttype_4' => $daily_rate_guesttype_4,
                    'daily_rate_minstay' => $daily_rate_minstay,
                    'daily_rate_maxstay' => $daily_rate_maxstay,
                ];
                $matched_ids = $rate_calendar_model
                    ->where([
                        'rate_calendar_host_id' => $host_id,
                        'daily_rate_code' =>
                            $data['daily_rate_code'],
                        'daily_rate_type' =>
                            $data['daily_rate_type'],
                        'daily_rate_day' =>
                            $data['daily_rate_day'],
                        'daily_rate_baserate' =>
                            $data['daily_rate_baserate'],
                    ])
                    ->findAll();
                if ($matched_ids != null) {
                    foreach ($matched_ids as $matched_id) {
                        if (
                            !$rate_calendar_model->update(
                                $matched_id[
                                    'daily_rate_id'
                                ],
                                $data
                            )
                        ) {
                            return $this->notifyError(
                                'Failed update',
                                'failed_update',
                                'rate_calendar'
                            );
                        }
                    }
                } else {
                    if (
                        !$rate_calendar_model->insert($data)
                    ) {
                        return $this->notifyError(
                            'Failed insert',
                            'failed_insert',
                            'rate_calendar'
                        );
                    }
                }
                $from = date(
                    'Y-m-d',
                    strtotime('+1 day', strtotime($from))
                );
            }
        }

        return $this->respond([
            'message' => 'Successfully updated.',
        ]);
    }
}
