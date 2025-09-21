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

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'idade.required' => 'O campo idade é obrigatório.',
            'nasc.required' => 'O campo data de nascimento é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'rg.required' => 'O campo RG é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'fone.required' => 'O campo telefone é obrigatório.',
            'altura.required' => 'O campo altura é obrigatório.',
            'peso.required' => 'O campo peso é obrigatório.',
            'img.linked' => 'O campo imagem deve ser um link válido.',
            'link.linked' => 'O campo link deve ser uma URL válida.',
            'pe.required' => 'O campo pé preferencial é obrigatório.',
            'posicao_principal.required' => 'O campo posição principal é obrigatório.',
            'posicao_secundaria.required' => 'O campo posição secundária é obrigatório.',
            'cirurgia.required' => 'O campo cirurgia é obrigatório.',
        ];
    }
}
