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
            'nome_completo' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'cidade' => 'required|string|max:255',
            'cpf' => 'required|string|digits:11|unique:Pessoas,cpf',
            'rg' => 'required|string|digits_between:7,9|unique:Pessoas,rg',
            'email' => 'required|string|max:255|unique:Pessoas,email',
            'telefone' => 'required|string|max:15',
            'altura_cm' => 'required|numeric|min:0',
            'peso_kg' => 'required|numeric|min:0',
            'foto_perfil_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'video_apresentacao_url' => 'nullable|url|max:255',

            # VALIDAÇÃO DAS OPÇÕES 
            'pe_preferido' => 'required|in:direito,esquerdo',
            'posicao_principal' => 'required|in:Goleiro,Lateral Direito,Zagueiro Esquerdo,Zagueiro Direito,Lateral Esquerdo,Volante,Meia,Ponta Direita,Ponta Esquerda,Atacante',
            'posicao_secundaria' => 'required|in:Goleiro,Lateral Direito,Zagueiro Esquerdo,Zagueiro Direito,Lateral Esquerdo,Volante,Meia,Ponta Direita,Ponta Esquerda,Atacante',
            'historico_lesoes_cirurgias' => 'required|in:sim,nao',
        ];
    }

    public function messages(): array
    {
        return [
            'nome_completo.required' => 'O campo nome é obrigatório.',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.digits' => 'O campo CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'O CPF informado já está em uso.',
            'rg.digits_between' => 'O campo RG deve ter entre 7 e 9 dígitos.',
            'rg.unique' => 'O RG informado já está em uso.',
            'rg.required' => 'O campo RG é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'telefone.required' => 'O campo telefone é obrigatório.',
            'altura_cm.required' => 'O campo altura é obrigatório.',
            'peso_kg.required' => 'O campo peso é obrigatório.',
            'pe_preferido.required' => 'O campo pé preferencial é obrigatório.',
            'posicao_principal.required' => 'O campo posição principal é obrigatório.',
            'posicao_secundaria.required' => 'O campo posição secundária é obrigatório.',
            'historico_lesoes_cirurgias.required' => 'O campo cirurgia é obrigatório.',
        ];
    }
}
