// Frontend/config.js
const CONFIG = {
    URL_API : "proyecto-production-7568.up.railway.app/api", 

    getAuthHeaders() {
        return {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        };
    }
};