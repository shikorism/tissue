import React, { createContext, useContext } from 'react';
import { useGetMe } from '../api/hooks';
import type { components } from '../api/schema';
import { ResponseError } from '../api/errors';

type User = components['schemas']['User'];

interface AuthContext {
    user?: User;
    isLoading: boolean;
}

const Context = createContext<AuthContext | null>(null);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const { data: user, isLoading, error } = useGetMe();
    if (error && error instanceof ResponseError && error.response.status !== 401) {
        throw error;
    }

    return <Context.Provider value={{ user, isLoading }}>{children}</Context.Provider>;
};

export const useCurrentUser = (): AuthContext => {
    const context = useContext(Context);
    if (!context) {
        throw new Error('useCurrentUser must be used within a AuthProvider');
    }
    return context;
};
