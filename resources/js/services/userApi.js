import axios from 'axios';

const userApi = axios.create({
  baseURL: '/api/user'
});

// Always attach latest token from localStorage
userApi.interceptors.request.use(config => {
  const token = localStorage.getItem('user_access_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default userApi;