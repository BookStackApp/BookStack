/**
 * @typedef FormattedResponse
 * @property {Headers} headers
 * @property {Response} original
 * @property {Object|String} data
 * @property {Boolean} redirected
 * @property {Number} status
 * @property {string} statusText
 * @property {string} url
 */

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
        return response.json();
    }

    return response.text();
}

export class HttpError extends Error {

    constructor(response, content) {
        super(response.statusText);
        this.data = content;
        this.headers = response.headers;
        this.redirected = response.redirected;
        this.status = response.status;
        this.statusText = response.statusText;
        this.url = response.url;
        this.original = response;
    }

}

/**
 * @param {String} method
 * @param {String} url
 * @param {Object} events
 * @return {XMLHttpRequest}
 */
export function createXMLHttpRequest(method, url, events = {}) {
    const csrfToken = document.querySelector('meta[name=token]').getAttribute('content');
    const req = new XMLHttpRequest();

    for (const [eventName, callback] of Object.entries(events)) {
        req.addEventListener(eventName, callback.bind(req));
    }

    req.open(method, url);
    req.withCredentials = true;
    req.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    return req;
}

/**
 * Create a new HTTP request, setting the required CSRF information
 * to communicate with the back-end. Parses & formats the response.
 * @param {String} url
 * @param {Object} options
 * @returns {Promise<FormattedResponse>}
 */
async function request(url, options = {}) {
    let requestUrl = url;

    if (!requestUrl.startsWith('http')) {
        requestUrl = window.baseUrl(requestUrl);
    }

    if (options.params) {
        const urlObj = new URL(requestUrl);
        for (const paramName of Object.keys(options.params)) {
            const value = options.params[paramName];
            if (typeof value !== 'undefined' && value !== null) {
                urlObj.searchParams.set(paramName, value);
            }
        }
        requestUrl = urlObj.toString();
    }

    const csrfToken = document.querySelector('meta[name=token]').getAttribute('content');
    const requestOptions = {...options, credentials: 'same-origin'};
    requestOptions.headers = {
        ...requestOptions.headers || {},
        baseURL: window.baseUrl(''),
        'X-CSRF-TOKEN': csrfToken,
    };

    const response = await fetch(requestUrl, requestOptions);
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
        throw new HttpError(response, content);
    }

    return returnData;
}

/**
 * Perform a HTTP request to the back-end that includes data in the body.
 * Parses the body to JSON if an object, setting the correct headers.
 * @param {String} method
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<FormattedResponse>}
 */
async function dataRequest(method, url, data = null) {
    const options = {
        method,
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

    return request(url, options);
}

/**
 * Perform a HTTP GET request.
 * Can easily pass query parameters as the second parameter.
 * @param {String} url
 * @param {Object} params
 * @returns {Promise<FormattedResponse>}
 */
export async function get(url, params = {}) {
    return request(url, {
        method: 'GET',
        params,
    });
}

/**
 * Perform a HTTP POST request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<FormattedResponse>}
 */
export async function post(url, data = null) {
    return dataRequest('POST', url, data);
}

/**
 * Perform a HTTP PUT request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<FormattedResponse>}
 */
export async function put(url, data = null) {
    return dataRequest('PUT', url, data);
}

/**
 * Perform a HTTP PATCH request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<FormattedResponse>}
 */
export async function patch(url, data = null) {
    return dataRequest('PATCH', url, data);
}

/**
 * Perform a HTTP DELETE request.
 * @param {String} url
 * @param {Object} data
 * @returns {Promise<FormattedResponse>}
 */
async function performDelete(url, data = null) {
    return dataRequest('DELETE', url, data);
}

export {performDelete as delete};
