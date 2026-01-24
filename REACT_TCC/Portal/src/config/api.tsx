import axios from 'axios';

// 1. Defina a URL base da sua API Laravel
export const API_BASE_URL = "http://127.0.0.1:8000/api/v1";

// 2. Crie uma instância do Axios
export const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json', // Importante para o Laravel não retornar HTML em erro
    },
});

// 3. Interceptor Mágico (O Segredo do Profissional)
// Toda vez que você fizer uma chamada (api.get, api.post), isso roda antes:
api.interceptors.request.use((config) => {
    // Tenta pegar o token salvo no navegador
    const token = localStorage.getItem('auth_token'); 

    // Se tiver token, adiciona no cabeçalho automaticamente
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
}, (error) => {
    return Promise.reject(error);
});