<?php

namespace App\Actions;

use App\Models\Option;
use App\Traits\Uploader;
use Illuminate\Support\Arr;

class OptionUpdate
{
    use Uploader;

    private array $optionData;
    private array $oldData = [];

    public function __construct()
    {
        $data = request()->all();
        $this->optionData = is_array($data) ? $data : [];
    }

    public function update(string $option_key)
    {
        $option = Option::query()->firstOrNew(
            [
                'key' => $option_key,
                'lang' => app()->getLocale(),
            ],
            [
                'value' => [],
            ]
        );

        $this->oldData = is_array($option->value)
            ? $option->value
            : json_decode($option->value ?? '{}', true);

        $this->uploadFiles();

        // 👇 Process badges from badges_string before saving
        if (isset($this->optionData['business_payments']['cards']) && is_array($this->optionData['business_payments']['cards'])) {
            foreach ($this->optionData['business_payments']['cards'] as &$card) {
                if (isset($card['badges_string'])) {
                    $card['badges'] = array_filter(array_map('trim', explode(',', $card['badges_string'])));
                } else {
                    $card['badges'] = [];
                }
            }
        }

        $option->value = $this->optionData;
        $option->save();
    }

    private function uploadFilesRecursive($data, $prefix = '')
    {
        if (!is_array($data)) {
            return [];
        }

        $uploadedData = [];

        foreach ($data as $key => $value) {
            $fileKey = $prefix . $key;

            if (is_array($value)) {
                $isAssoc = Arr::isAssoc($value);

                if ($isAssoc) {
                    $uploadedData[$key] = $this->uploadFilesRecursive($value, $fileKey . '.');
                } else {
                    $uploadedData[$key] = [];
                    foreach ($value as $index => $item) {
                        $uploadedData[$key][$index] = $this->uploadFilesRecursive($item, $fileKey . '.' . $index . '.');
                    }
                }
            } elseif ($value instanceof \Illuminate\Http\UploadedFile) {
                $oldFilePath = $this->getOldFilePath($fileKey);

                if ($oldFilePath !== null && is_file($oldFilePath)) {
                    unlink($oldFilePath);
                }

                $uploadedData[$key] = (string) $this->uploadFile($fileKey);
            } else {
                $uploadedData[$key] = $value;
            }
        }

        return $uploadedData;
    }

    private function getOldFilePath($fileKey)
    {
        $filePath = data_get($this->oldData, $fileKey);

        if ($filePath !== null && file_exists(public_path($filePath))) {
            return public_path($filePath);
        }

        return null;
    }

    public function uploadFiles()
    {
        $requestData = request()->all();
        $modifiedOptionData = $this->uploadFilesRecursive(is_array($requestData) ? $requestData : []);
        $this->optionData = $modifiedOptionData;
    }
}
