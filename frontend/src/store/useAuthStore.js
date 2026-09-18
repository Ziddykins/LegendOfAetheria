import { create } from 'zustand';
import api from '../api/client';
import { useUIStore } from './useUIStore';

const savedToken = localStorage.getItem('loa_token');
let savedAccount = null;
try {
  savedAccount = JSON.parse(localStorage.getItem('loa_account') || 'null');
} catch {
  savedAccount = null;
}

export const useAuthStore = create((set, get) => ({
  token: savedToken,
  account: savedAccount,
  isAuthenticated: Boolean(savedToken),
  isLoading: false,
  error: null,

  login: async (email, password) => {
    set({ isLoading: true, error: null });
    try {
      const response = await api.post('/auth', { email, password });
      const { token, account } = response.data;

      if (!token) {
        throw new Error(response.data.message || 'Login failed');
      }

      localStorage.setItem('loa_token', token);
      localStorage.setItem('loa_account', JSON.stringify(account || { email }));

      set({
        token,
        account: account || { email },
        isAuthenticated: true,
        isLoading: false,
        error: null,
      });

      useUIStore.getState().setApiOffline(false);
      useUIStore.getState().notify(`Welcome back, ${email}!`, 'success');
      return { success: true };
    } catch (err) {
      const message =
        err.response?.data?.error ||
        err.response?.data?.message ||
        (err.code === 'ERR_NETWORK'
          ? 'Cannot reach Express API server at http://localhost:3000'
          : err.message || 'Authentication failed');

      if (err.code === 'ERR_NETWORK') {
        useUIStore.getState().setApiOffline(true);
      }

      set({ isLoading: false, error: message });
      return { success: false, error: message };
    }
  },

  demoLogin: () => {
    const demoToken = 'demo_dev_token_' + Date.now();
    const demoAccount = { id: 1, email: 'ziddy@memelife.ca', privileges: 'ADMINISTRATOR' };
    localStorage.setItem('loa_token', demoToken);
    localStorage.setItem('loa_account', JSON.stringify(demoAccount));
    set({
      token: demoToken,
      account: demoAccount,
      isAuthenticated: true,
      isLoading: false,
      error: null,
    });
    useUIStore.getState().notify('Entered Aetheria in Test Access Mode!', 'success');
    return { success: true };
  },

  register: async ({ email, password, characterName, race }) => {
    set({ isLoading: true, error: null });
    try {
      const response = await api.post('/v1/accounts', {
        email,
        password,
        name: characterName,
        race,
      });

      set({ isLoading: false });
      useUIStore.getState().notify('Account created! Please log in.', 'success');
      return { success: true, data: response.data };
    } catch (err) {
      const message =
        err.response?.data?.error ||
        err.response?.data?.message ||
        (err.code === 'ERR_NETWORK'
          ? 'API server unreachable'
          : 'Registration failed');

      set({ isLoading: false, error: message });
      return { success: false, error: message };
    }
  },

  logout: () => {
    localStorage.removeItem('loa_token');
    localStorage.removeItem('loa_account');
    localStorage.removeItem('loa_active_char');
    set({
      token: null,
      account: null,
      isAuthenticated: false,
      error: null,
    });
    useUIStore.getState().notify('Signed out successfully.', 'info');
  },
}));
