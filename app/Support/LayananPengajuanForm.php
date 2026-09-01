<?php

namespace App\Support;

use App\Models\Layanan;
use App\Models\LayananField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayananPengajuanForm
{
    /** @return array{rules: array<string, mixed>, attributes: array<string, string>, messages: array<string, string>} */
    public static function validation(Layanan $layanan): array
    {
        $rules = [];
        $attributes = [];
        $messages = [];

        foreach ($layanan->fields as $field) {
            $key = 'fields.'.$field->field_key;
            $fieldRules = $field->is_required ? ['required'] : ['nullable'];

            switch ($field->type) {
                case LayananField::TYPE_EMAIL:
                    $fieldRules[] = 'email';
                    $fieldRules[] = 'max:255';
                    break;
                case LayananField::TYPE_TEL:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:30';
                    break;
                case LayananField::TYPE_NUMBER:
                    $fieldRules[] = 'numeric';
                    break;
                case LayananField::TYPE_DATE:
                    $fieldRules[] = 'date';
                    break;
                case LayananField::TYPE_PDF:
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimes:pdf';
                    $fieldRules[] = 'max:10240';
                    break;
                case LayananField::TYPE_IMAGE:
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'mimes:jpg,jpeg,png';
                    $fieldRules[] = 'max:5120';
                    break;
                case LayananField::TYPE_FILE:
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:10240';
                    break;
                case LayananField::TYPE_SELECT:
                case LayananField::TYPE_RADIO:
                    $fieldRules[] = 'string';
                    if ($field->options) {
                        $fieldRules[] = Rule::in($field->options);
                    }
                    break;
                case LayananField::TYPE_CHECKBOX:
                    $fieldRules = $field->is_required ? ['accepted'] : ['nullable', 'boolean'];
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';
            }

            $rules[$key] = $fieldRules;
            $attributes[$key] = $field->name;
            $messages["{$key}.required"] = "{$field->name} wajib diisi.";
            $messages["{$key}.accepted"] = "{$field->name} wajib dicentang.";
            $messages["{$key}.email"] = "{$field->name} harus berupa email yang valid.";
            $messages["{$key}.file"] = "{$field->name} wajib diunggah.";
            $messages["{$key}.mimes"] = "Format file {$field->name} tidak sesuai.";
            $messages["{$key}.max"] = "{$field->name} melebihi ukuran maksimum.";
        }

        return ['rules' => $rules, 'attributes' => $attributes, 'messages' => $messages];
    }

    /** @return array<string, mixed> */
    public static function collectData(Layanan $layanan, Request $request): array
    {
        $data = [];

        foreach ($layanan->fields as $field) {
            $key = $field->field_key;

            if ($field->isFileType()) {
                if ($request->hasFile("fields.{$key}")) {
                    $data[$key] = $request->file("fields.{$key}")
                        ->store("layanan-pengajuans/uploads/{$layanan->id}", 'public');
                }

                continue;
            }

            if ($field->type === LayananField::TYPE_CHECKBOX) {
                $data[$key] = $request->boolean("fields.{$key}");

                continue;
            }

            $value = $request->input("fields.{$key}");
            $data[$key] = $value === '' ? null : $value;
        }

        return $data;
    }
}
