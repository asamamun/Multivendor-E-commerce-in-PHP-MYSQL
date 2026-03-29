// Base URL — change this to match your local server
const BASE_URL = 'http://localhost/round68/php/classes/class34Project/Multivendor-E-commerce-in-PHP-MYSQL/apis';

// Token storage helpers
const Auth = {
    setToken: (token) => localStorage.setItem('api_token', token),
    getToken: () => localStorage.getItem('api_token'),
    clear:    () => localStorage.removeItem('api_token'),
    headers:  () => ({
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${Auth.getToken()}`
    })
};

// Generic fetch wrapper
async function apiFetch(endpoint, options = {}) {
    const url = `${BASE_URL}/${endpoint}`;
    const res  = await fetch(url, options);
    const data = await res.json();
    return { status: res.status, data };
}

// Log helper for the test UI
function log(containerId, label, result) {
    const el = document.getElementById(containerId);
    const pre = document.createElement('pre');
    pre.className = result.data?.success === false ? 'error' : 'success';
    pre.textContent = `[${label}] HTTP ${result.status}\n${JSON.stringify(result.data, null, 2)}`;
    el.prepend(pre);
}
