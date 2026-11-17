import axios from 'axios';

export const TOKEN_KEY = 'elademate_token';

const api = axios.create({
    baseURL: (import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api').replace(/\/$/, ''),
    headers: {
        Accept: 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem(TOKEN_KEY);

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            window.dispatchEvent(new CustomEvent('elademate:unauthorized'));
        }

        return Promise.reject(error);
    },
);

export function getApiError(error, fallback = 'Something went wrong. Please try again.') {
    const data = error?.response?.data;

    return {
        message: data?.message || fallback,
        errors: data?.errors || {},
        status: error?.response?.status,
    };
}

export default api;
