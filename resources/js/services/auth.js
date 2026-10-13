
import axios from 'axios';
import api from './api';

const TOKEN_KEY = 'freelance_agent_token';

const auth = {
    getToken() {
        return localStorage.getItem(TOKEN_KEY);
    },

    setToken(token) {
        localStorage.setItem(TOKEN_KEY, token);
    },

    removeToken() {
        localStorage.removeItem(TOKEN_KEY);
    },

    isAuthenticated() {
        return !!this.getToken();
    },

    async login(email, password) {
        // Le login est public : pas besoin de token
        const response = await axios.post('/api/login', {
            email,
            password,
        });

        this.setToken(response.data.token);

        return response.data;
    },

    async logout() {
        try {
            if (this.getToken()) {
                await api.post('/logout');
            }
        } finally {
            this.removeToken();
        }
    },

    async me() {
        if (!this.getToken()) {
            return null;
        }

        const response = await api.get('/me');

        return response.data.user;
    },
};

export default auth;