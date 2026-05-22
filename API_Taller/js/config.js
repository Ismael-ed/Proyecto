// Frontend/config.js
const CONFIG = {
    API_URL: 'proyecto-production-7568.up.railway.app/api', 

    getAuthHeaders() {
        return {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        };
    }
};