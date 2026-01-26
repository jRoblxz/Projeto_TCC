import axios from 'axios';

// 1. Defina a URL base dinâmica
// Se existir a variável de ambiente (na Vercel), usa ela. Se não, usa o localhost.
export const API_BASE_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api/v1";

// 2. Crie uma instância do Axios usando a constante acima
export const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json' // Importante para o Laravel retornar JSON, não HTML em erros
    }
});

// 3. Interceptor Mágico
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token'); 
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});