<?php

namespace App\Http\Requests\Care;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeedsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_gulma' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'pengendalian' => 'required|string',
            'jenis_pestisida' => 'required|string|max:255',
            'klasifikasi_berdasarkan_cara_kerja' => 'required|string|max:255',
            'golongan_senyawa_kimia' => 'required|string',
            'bahan_aktif' => 'required|string|max:255',
        ];
    }
}
