<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'idade' => 'required|integer|min:0',
            'nasc' => 'required|date',
            'cidade' => 'required|string|max:255',
            'cpf' => 'required|integer|digits:11|unique:users,cpf',
            'rg' => 'required|integer|digits_between:7,9|unique:users,rg',
            'email' => 'required|string|email|max:255|unique:users,email',
            'fone' => 'required|string|max:15',
            'altura' => 'required|numeric|min:0',
            'peso' => 'required|numeric|min:0',
            'img' => 'linked|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'linked|url|max:255',
            # VALIDAÇÃO DAS OPÇÕES 
            'pe' => 'required|in:direito,esquerdo',
            'posicao_principal' => 'required|in:gol,ld,le,ze,zd,vol,mei,pe,pd,ata',
            'posicao_secundaria' => 'required|in:gol,ld,le,ze,zd,vol,mei,pe,pd,ata',
            'cirurgia' => 'required|in:sim,nao',
        ];
    }
}
