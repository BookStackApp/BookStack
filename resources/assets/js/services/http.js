import axios from "axios"

function instance() {
    let axiosInstance = axios.create({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=token]').getAttribute('content'),
            'baseURL': window.baseUrl('')
        }
    });
    axiosInstance.interceptors.request.use(resp => {
        return resp;
    }, err => {
        if (typeof err.response === "undefined" || typeof err.response.data === "undefined") return Promise.reject(err);
        if (typeof err.response.data.error !== "undefined") window.$events.emit('error', err.response.data.error);
        if (typeof err.response.data.message !== "undefined") window.$events.emit('error', err.response.data.message);
    });
    return axiosInstance;
}


export default instance;