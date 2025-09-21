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
        $userId = $this->route('user');
        return [
            'nome' => 'required|string|max:255',
            'idade' => 'required|integer|min:0',
            'nasc' => 'required|date',
            'cidade' => 'required|string|max:255',
            'cpf' => 'required|string|digits:11|unique:pessoas,cpf' . ($userId ? ",$userId->id" : null),
            'rg' => 'required|string|digits_between:7,9|unique:pessoas,rg' . ($userId ? ",$userId->id" : null),
            'email' => 'required|string|email|max:255|unique:pessoas,email' . ($userId ? ",$userId->id" : null),
            'fone' => 'required|string|max:15',
            'altura' => 'required|numeric|min:0',
            'peso' => 'required|numeric|min:0',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'required|url|max:255',
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
            'cpf.digits' => 'O campo CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'O CPF informado já está em uso.',
            'rg.digits_between' => 'O campo RG deve ter entre 7 e 9 dígitos.',
            'rg.unique' => 'O RG informado já está em uso.',
            'rg.required' => 'O campo RG é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'fone.required' => 'O campo telefone é obrigatório.',
            'altura.required' => 'O campo altura é obrigatório.',
            'peso.required' => 'O campo peso é obrigatório.',
            'img.required' => 'O campo imagem é obrigatório.',
            'img.image' => 'O arquivo deve ser uma imagem.',
            'img.mimes' => 'A imagem deve ser jpeg, png, jpg ou gif.',
            'img.max' => 'A imagem deve ter no máximo 2MB.',
            'link.required' => 'O campo link deve ser uma URL válida.',
            'pe.required' => 'O campo pé preferencial é obrigatório.',
            'posicao_principal.required' => 'O campo posição principal é obrigatório.',
            'posicao_secundaria.required' => 'O campo posição secundária é obrigatório.',
            'cirurgia.required' => 'O campo cirurgia é obrigatório.',
        ];
    }
}
