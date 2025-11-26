import axios from 'axios';

// Crear instancia de axios
const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
});

// Interceptor para agregar token JWT
api.interceptors.request.use(config => {
    const token = localStorage.getItem('jwt_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor para manejar respuestas
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            console.error('Token inválido o expirado');
        }
        return Promise.reject(error);
    }
);

export default api;
