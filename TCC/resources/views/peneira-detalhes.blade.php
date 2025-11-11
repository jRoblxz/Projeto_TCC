@extends('navbar')
@section('content')
    <div class="container">

        <div style="margin-bottom: 20px;">
            <h2>Gerenciando a Peneira: {{ $peneiras->nome_evento }}</h2>
            <p>
                <strong>Data:</strong>
                {{ $peneiras->data_evento ? \Carbon\Carbon::parse($peneiras->data_evento)->format('d/m/Y \à\s H:i') : 'N/A' }}
            </p>
            <p><strong>Local:</strong> {{ $peneiras->local }}</p>
            <p><strong>Status:</strong> <span
                    class="card-status-peneira status-{{ $peneiras->status }}">{{ $peneiras->status }}</span></p>
            <p><strong>Sub-Divisão (Idade):</strong> {{ $peneiras->sub_divisao }}</p>
            <p><strong>Descrição:</strong> {{ $peneiras->descricao }}</p>
        </div>

        <hr>

        <div class="gerar-equipes-container"
            style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; margin-top: 20px; background: #f9f9f9;">

            <h3>Gerador de Equipes</h3>

            <form action="{{ route('peneiras.montarEquipes', ['id' => $peneiras->id]) }}" method="POST">
                @csrf <p>Clique no botão abaixo para montar automaticamente as equipes com os jogadores inscritos e
                    disponíveis.</p>

                <button type="submit" class="btn-peneira btn-primary" style="background-color: #007bff; color: white;">
                    Gerar Equipes Agora
                </button>
            </form>

            @if (session('success'))
                <div style="color: green; margin-top: 10px; font-weight: bold;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div style="color: red; margin-top: 10px; font-weight: bold;">
                    {{ session('error') }}
                </div>
            @endif
        </div>
        <hr>

        <div style="margin-top: 20px;">
            <h3>Jogadores Inscritos</h3>
            <p>(Em breve...)</p>
            <h3>Equipes Geradas</h3>
            <p>(Em breve...)</p>
        </div>

        <a href="{{ route('peneira.index') }}" class="btn-peneira btn-secondary" style="margin-top: 20px;">
            &larr; Voltar para a Lista de Peneiras
        </a>

    </div>
@endsection