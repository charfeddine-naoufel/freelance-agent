
import axios from 'axios';
import auth from './auth';

const api = axios.create({
    baseURL: '/api',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

// Ajouter automatiquement le token à chaque requête API
api.interceptors.request.use(
    (config) => {
        const token = auth.getToken();

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => Promise.reject(error)
);

// Si le token n'est plus valide, déconnecter l'utilisateur
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            auth.removeToken();

            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }

        return Promise.reject(error);
    }
);

export default api;