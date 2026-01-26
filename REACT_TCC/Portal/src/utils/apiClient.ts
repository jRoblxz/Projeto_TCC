import { API_BASE_URL } from "../config/api";
import toast from "react-hot-toast";

// Lógica de Trava/fila
let isRefreshing = false;
let refreshPromise: Promise<string | null> | null = null;

//Funçaõ de refresh do token
async function refreshAccessToken(): Promise<string | null> {
  const token = localStorage.getItem("token");
  if (!token) {
    localStorage.removeItem("token");
    throw new Error("Token não encontrado. Faça login novamente.");
  }

  try {
    const response = await fetch(`${API_BASE_URL}auth/refresh`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      localStorage.removeItem("token");
      throw new Error("Sessão expirada. Faça login novamente.");
    }

    const result = await response.json();
    const newToken = result.token; //

    if (!newToken) {
      throw new Error("Novo token não recebido da API.");
    }

    localStorage.setItem("token", newToken);
    return newToken;
  } catch (error) {
    // Se o refresh falhar, limpa o token e rejeita a promessa
    localStorage.removeItem("token");
    return null;
  }
}

// Wrapper para chamadas à API com auto-refresh
export async function apiFetch(url: string, options: any = {}, retry = true): Promise<Response> {
  let token = localStorage.getItem("token");

  const headers = {
    "Content-Type": "application/json",
    Authorization: token ? `Bearer ${token}` : "",
    ...options.headers,
  };

  const response = await fetch(`${API_BASE_URL}${url}`, {
    ...options,
    headers,
  });

  // Se o token expirou (401), tenta atualizar
  if (response.status === 401 && retry) {
    if (!isRefreshing) {
      // 1. A PRIMEIRA chamada 401 entra aqui.
      isRefreshing = true;
      // garante que a trava será liberada, mesmo se o refresh falhar
      refreshPromise = refreshAccessToken().finally(() => {
        isRefreshing = false;
        refreshPromise = null;
      });
    }

    try {
      // 2. TODAS as chamadas esperam pela *mesma* promessa de refresh
      const newToken = await refreshPromise;

      if (newToken) {
        // 3. Tenta a requisição original novamente, com o novo token
        const newHeaders = {
          ...headers,
          Authorization: `Bearer ${newToken}`,
        };
        // Passa 'false' para 'retry' para evitar loop infinito
        return await apiFetch(url, { ...options, headers: newHeaders }, false);
      } else {
        // Se o refresh falhou (retornou null), joga o erro
        throw new Error("Sessão expirada.");
      }
    } catch (err) {
      toast.error("Sessão expirada. Faça login novamente.");
      // Redireciona para o login se o refresh falhar
      // window.location.href = '/login';
      throw err;
    }
  }

  return response;
}
