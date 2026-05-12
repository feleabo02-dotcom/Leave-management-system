import React from 'react';
import { createRoot } from 'react-dom/client';
import ErpDashboard from './components/ErpDashboard';

const el = document.getElementById('react-dashboard');
if (el) {
    const props = Object.assign({}, el.dataset);
    createRoot(el).render(<ErpDashboard {...props} />);
}
