
/**
 * Perform a HTTP GET request.
 * Can easily pass query parameters as the second parameter.
 * @param {String} url
 * @param {Object} params
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function get(url, params = {}) {
    return request(url, {
        method: 'GET',
        params,
    });
}

/**
 * Perform a HTTP POST request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function post(url, data = null) {
    return dataRequest('POST', url, data);
}

/**
 * Perform a HTTP PUT request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function put(url, data = null) {
    return dataRequest('PUT', url, data);
}

/**
 * Perform a HTTP PATCH request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function patch(url, data = null) {
    return dataRequest('PATCH', url, data);
}

/**
 * Perform a HTTP DELETE request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function performDelete(url, data = null) {
    return dataRequest('DELETE', url, data);
}

/**
 * Perform a HTTP request to the back-end that includes data in the body.
 * Parses the body to JSON if an object, setting the correct headers.
 * @param {String} method
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function dataRequest(method, url, data = null) {
    const options = {
        method: method,
        body: data,
    };

    // Send data as JSON if a plain object
    if (typeof data === 'object' && !(data instanceof FormData)) {
        options.headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };
        options.body = JSON.stringify(data);
    }

    // Ensure FormData instances are sent over POST
    // Since Laravel does not read multipart/form-data from other types
    // of request. Hence the addition of the magic _method value.
    if (data instanceof FormData && method !== 'post') {
        data.append('_method', method);
        options.method = 'post';
    }

    return request(url, options)
}

/**
 * Create a new HTTP request, setting the required CSRF information
 * to communicate with the back-end. Parses & formats the response.
 * @param {String} url
 * @param {Object} options
 * @returns {Promise<{headers: Headers, original: Response, data: (Object|String), redirected: boolean, statusText: string, url: string, status: number}>}
 */
async function request(url, options = {}) {
    if (!url.startsWith('http')) {
        url = window.baseUrl(url);
    }

    if (options.params) {
        const urlObj = new URL(url);
        for (let paramName of Object.keys(options.params)) {
            const value = options.params[paramName];
            if (typeof value !== 'undefined' && value !== null) {
                urlObj.searchParams.set(paramName, value);
            }
        }
        url = urlObj.toString();
    }

    const csrfToken = document.querySelector('meta[name=token]').getAttribute('content');
    options = Object.assign({}, options, {
        'credentials': 'same-origin',
    });
    options.headers = Object.assign({}, options.headers || {}, {
        'baseURL': window.baseUrl(''),
        'X-CSRF-TOKEN': csrfToken,
    });

    const response = await fetch(url, options);
    const content = await getResponseContent(response);
    const returnData = {
        data: content,
        headers: response.headers,
        redirected: response.redirected,
        status: response.status,
        statusText: response.statusText,
        url: response.url,
        original: response,
    };

    if (!response.ok) {
        throw returnData;
    }

    return returnData;
}

/**
 * Get the content from a fetch response.
 * Checks the content-type header to determine the format.
 * @param {Response} response
 * @returns {Promise<Object|String>}
 */
async function getResponseContent(response) {
    if (response.status === 204) {
        return null;
    }

    const responseContentType = response.headers.get('Content-Type') || '';
    const subType = responseContentType.split(';')[0].split('/').pop();

    if (subType === 'javascript' || subType === 'json') {
        return await response.json();
    }

    return await response.text();
}

export default {
    get: get,
    post: post,
    put: put,
    patch: patch,
    delete: performDelete,
};