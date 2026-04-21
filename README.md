<div align="center">

  <h1>⚽ Sistema de Gestão de Peneiras e Avaliação de Atletas</h1>

  <p>
    Uma solução digital completa para modernizar a gestão de seletivas de futebol, substituindo o papel por dados e inteligência.
  </p>

  <p>
    <a href="#-sobre-o-projeto">Sobre</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <a href="#-tecnologias">Tecnologias</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <a href="#-funcionalidades">Funcionalidades</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <a href="#-como-rodar">Como Rodar</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <a href="#-deploy">Deploy</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
    <a href="#-colaboradores">Colaboradores</a>
  </p>

  <p>
    <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" />
    <img src="https://img.shields.io/badge/Laravel-10/11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
    <img src="https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB" />
    <img src="https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
  </p>
</div>

---.

## 🚀 Sobre o Projeto

Este repositório contém o código-fonte do nosso **Projeto de TCC** focado na gestão esportiva. O sistema foi desenvolvido para facilitar o trabalho de olheiros, treinadores e clubes, automatizando o processo de "peneiras" (seletivas de futebol).

O objetivo principal é eliminar o uso de pranchetas de papel, permitindo que as avaliações sejam feitas em tempo real via tablet ou celular, com os dados centralizados e acessíveis.

## ✨ Funcionalidades

* **📈 Painel Administrativo (Dashboard):** Visão geral com estatísticas de inscritos, distribuição por posições e peneiras ativas.
* **📅 Gestão de Peneiras:** Criação, edição e agendamento de eventos de seletiva.
* **📝 Inscrição de Candidatos:** Formulário público para atletas se cadastrarem nas peneiras disponíveis.
* **⭐ Avaliação de Jogadores:** Treinadores podem atribuir notas, observações e características técnicas a cada jogador em tempo real.
* **⚖️ Gerador de Times:** Algoritmo inteligente que monta times automaticamente (Time A vs Time B) baseando-se nas notas e posições para garantir equilíbrio.
* **drag_and_drop Editor de Times:** Interface visual interativa para ajustes manuais nas escalações.

---

## 🛠 Tecnologias Utilizadas

### Backend (API)
* **Linguagem:** PHP 8.x
* **Framework:** Laravel 10/11
* **Banco de Dados:** MySQL
* **Armazenamento:** Google Cloud Storage (Fotos) ou Local

### Frontend (Portal)
* **Framework:** React (Vite)
* **Linguagem:** TypeScript
* **Estilização:** Tailwind CSS, Shadcn/UI & Material UI
* **Ícones:** Lucide React
* **Requisições:** Axios

---

## 💻 Como Rodar o Projeto Localmente

Siga o passo a passo abaixo para configurar o ambiente de desenvolvimento na sua máquina.

### Pré-requisitos
* [PHP](https://www.php.net/downloads) (8.1+)
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/) (18+)
* [MySQL](https://www.mysql.com/) (ou MariaDB/XAMPP/Laragon)
* [Git](https://git-scm.com/)

### 1️⃣ Configurando o Backend (Laravel)

1.  Acesse a pasta do backend:
    ```bash
    cd TCC
    ```

2.  Instale as dependências do PHP:
    ```bash
    composer install
    ```

3.  Configure o arquivo de ambiente:
    * Duplique o arquivo `.env.example` e renomeie para `.env`.
    * Abra o `.env` e configure o banco de dados (Atenção ao DB_HOST):
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nome_do_seu_banco
    DB_USERNAME=root
    DB_PASSWORD=sua_senha
    ```

4.  Gere a chave da aplicação:
    ```bash
    php artisan key:generate
    ```

5.  **Opção A - Migrations (Recomendado):** Rode as migrações e seeders:
    ```bash
    php artisan migrate --seed
    ```

6.  Crie o link para imagens:
    ```bash
    php artisan storage:link
    ```

7.  Inicie o servidor:
    ```bash
    php artisan serve
    ```

### 2️⃣ Configurando o Banco de Dados (Via SQL)

*Caso prefira não usar migrations:*
1.  Abra seu gerenciador de banco (ex: phpMyAdmin).
2.  Crie um banco de dados vazio.
3.  Importe o arquivo `peneira_db.sql` localizado na raiz do projeto.

### 3️⃣ Configurando o Frontend (React)

1.  Em outro terminal, acesse a pasta do portal:
    ```bash
    cd REACT_TCC/Portal
    ```

2.  Instale as dependências:
    ```bash
    npm install
    ```

3.  Configure a API:
    * Crie um arquivo `.env` na raiz da pasta `Portal`.
    * Defina a URL do backend:
    ```env
    VITE_API_URL=http://localhost:8000/api/v1
    ```

4.  Inicie o projeto:
    ```bash
    npm run dev
    ```

---

## 📦 Deploy

O projeto está no ar para testes e demonstração.

* **Backend & Banco de Dados:** Hospedados no [Railway](https://railway.app/)
* **Frontend:** Hospedado na [Vercel](https://vercel.com/)

🔗 **Acesse o projeto aqui:** [Projeto TCC](https://projeto-tcc-jade.vercel.app/instrucoes)

---

## 🤝 Colaboradores

Projeto desenvolvido com dedicação para fins acadêmicos.

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/jroblxz">
        <img src="https://github.com/jroblxz.png" width="100px;" alt="Foto do João Roblez"/><br>
        <sub>
          <b>João Roblez</b>
        </sub>
      </a>
    </td>
    <td align="center">
      <a href="https://github.com/Kaynan1101">
        <img src="https://github.com/Kaynan1101.png" width="100px;" alt="Foto do Kaynan Lima"/><br>
        <sub>
          <b>Kaynan Lima</b>
        </sub>
      </a>
    </td>
    <td align="center">
      <a href="https://github.com/JoaoPirasol">
        <img src="https://github.com/JoaoPirasol.png" width="100px;" alt="Foto do João Pirasol"/><br>
        <sub>
          <b>João Pirasol</b>
        </sub>
      </a>
    </td>
  </tr>
</table>

---

<div align="center">
  Feito por João Roblez e Kaynan Lima
</div>
