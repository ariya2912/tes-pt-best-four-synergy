import axios from 'axios';

const API = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
});

export const getLeads = () => API.get('/leads');
export const uploadExcel = (formData) => API.post('/upload-excel', formData);
export const getReports = () => API.get('/report');
export const saveReport = (data) => API.post('/report', data);
export const deleteReport = (id) => API.delete(`/report/${id}`);