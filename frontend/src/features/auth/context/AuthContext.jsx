import { createContext, useCallback, useEffect, useMemo, useState } from 'react';
import api, { getApiError, TOKEN_KEY } from '@/services/api/client';

export const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [token, setToken] = useState(() => localStorage.getItem(TOKEN_KEY));
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(Boolean(localStorage.getItem(TOKEN_KEY)));

    const clearSession = useCallback(() => {
        localStorage.removeItem(TOKEN_KEY);
        setToken(null);
        setUser(null);
        setLoading(false);
    }, []);

    useEffect(() => {
        const handleUnauthorized = () => clearSession();
        window.addEventListener('elademate:unauthorized', handleUnauthorized);
        return () => window.removeEventListener('elademate:unauthorized', handleUnauthorized);
    }, [clearSession]);

    useEffect(() => {
        let active = true;
        if (!token) {
            setLoading(false);
            setUser(null);
            return undefined;
        }
        setLoading(true);
        api.get('/user')
            .then((response) => { if (active) setUser(response.data.data); })
            .catch(() => { if (active) clearSession(); })
            .finally(() => { if (active) setLoading(false); });
        return () => { active = false; };
    }, [token, clearSession]);

    const login = useCallback(async (credentials) => {
        const response = await api.post('/login', credentials);
        const { user: nextUser, token: nextToken } = response.data.data;
        localStorage.setItem(TOKEN_KEY, nextToken);
        setToken(nextToken);
        setUser(nextUser);
        setLoading(false);
        return response.data;
    }, []);

    const register = useCallback(async (payload) => {
        const response = await api.post('/register', payload);
        const { user: nextUser, token: nextToken } = response.data.data;
        localStorage.setItem(TOKEN_KEY, nextToken);
        setToken(nextToken);
        setUser(nextUser);
        setLoading(false);
        return response.data;
    }, []);

    const logout = useCallback(async () => {
        try {
            await api.post('/logout');
        } catch (error) {
            if (error.response?.status !== 401) {
                throw getApiError(error, 'Unable to log out right now.');
            }
        } finally {
            clearSession();
        }
    }, [clearSession]);

    const refreshUser = useCallback(async () => {
        const response = await api.get('/user');
        setUser(response.data.data);
        return response.data.data;
    }, []);

    const updateProfile = useCallback(async (payload) => {
        const response = await api.post('/profile', payload);
        setUser(response.data.data.user);
        return response.data;
    }, []);

    const changePassword = useCallback(async (payload) => {
        const response = await api.put('/profile/password', payload);
        return response.data;
    }, []);

    const value = useMemo(() => ({
        user, token, loading, isAuthenticated: Boolean(token && user),
        login, register, logout, refreshUser, updateProfile, changePassword,
    }), [user, token, loading, login, register, logout, refreshUser, updateProfile, changePassword]);

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}