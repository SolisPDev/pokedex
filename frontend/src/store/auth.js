import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    initialize() {
      const storedToken = localStorage.getItem('token');
      const storedUser = localStorage.getItem('user');
      if (storedToken && storedUser) {
        this.token = storedToken;
        try {
          this.user = JSON.parse(storedUser);
        } catch (e) {
          this.logout();
        }
      }
    },
    async login(email, password) {
      try {
        const response = await api.post('/auth/login', { email, password });
        const { access_token, user } = response.data;
        
        this.token = access_token;
        this.user = user;
        
        localStorage.setItem('token', access_token);
        localStorage.setItem('user', JSON.stringify(user));
        return true;
      } catch (error) {
        console.error('Login error', error);
        throw error;
      }
    },
    async register(name, email, password) {
      try {
        const response = await api.post('/auth/register', { name, email, password });
        const { access_token, user } = response.data;
        
        this.token = access_token;
        this.user = user;
        
        localStorage.setItem('token', access_token);
        localStorage.setItem('user', JSON.stringify(user));
        return true;
      } catch (error) {
        console.error('Registration error', error);
        throw error;
      }
    },
    async logout() {
      try {
        await api.post('/auth/logout');
      } catch (error) {
        console.error('Logout error', error);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    }
  }
});
