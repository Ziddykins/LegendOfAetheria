import axios from 'axios';

const api = axios.create({
  baseURL: '', // Uses Vite proxy config for /auth and /v1
  timeout: 8000,
  headers: {
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('loa_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Clear token if expired/invalid
      localStorage.removeItem('loa_token');
      localStorage.removeItem('loa_account');
    }
    return Promise.reject(error);
  }
);

export const checkApiHealth = async () => {
  try {
    const res = await api.get('/auth', { timeout: 3000 });
    return res.status >= 200 && res.status < 500;
  } catch (err) {
    return false;
  }
};

export default api;
