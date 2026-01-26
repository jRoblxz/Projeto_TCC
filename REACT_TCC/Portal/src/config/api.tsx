import axios from 'axios';

// 1. Defina a URL base dinâmica (AGORA CORRIGIDO)
export const API_BASE_URL = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000/api/v1";

// 2. Crie uma instância do Axios
export const api = axios.create({
    baseURL: API_BASE_URL, // Usa a constante inteligente definida acima
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
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