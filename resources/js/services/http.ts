type ResponseData = Record<any, any>|string;

type RequestOptions = {
    params?: Record<string, string>,
    headers?: Record<string, string>
};

type FormattedResponse = {
    headers: Headers;
    original: Response;
    data: ResponseData;
    redirected: boolean;
    status: number;
    statusText: string;
    url: string;
};

export class HttpError extends Error implements FormattedResponse {

    data: ResponseData;
    headers: Headers;
    original: Response;
    redirected: boolean;
    status: number;
    statusText: string;
    url: string;

    constructor(response: Response, content: ResponseData) {
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

export class HttpManager {

    /**
     * Get the content from a fetch response.
     * Checks the content-type header to determine the format.
     */
    protected async getResponseContent(response: Response): Promise<ResponseData|null> {
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

    createXMLHttpRequest(method: string, url: string, events: Record<string, (e: Event) => void> = {}): XMLHttpRequest {
        const csrfToken = document.querySelector('meta[name=token]')?.getAttribute('content');
        const req = new XMLHttpRequest();

        for (const [eventName, callback] of Object.entries(events)) {
            req.addEventListener(eventName, callback.bind(req));
        }

        req.open(method, url);
        req.withCredentials = true;
        req.setRequestHeader('X-CSRF-TOKEN', csrfToken || '');

        return req;
    }

    /**
     * Create a new HTTP request, setting the required CSRF information
     * to communicate with the back-end. Parses & formats the response.
     */
    protected async request(url: string, options: RequestOptions & RequestInit = {}): Promise<FormattedResponse> {
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

        const csrfToken = document.querySelector('meta[name=token]')?.getAttribute('content') || '';
        const requestOptions: RequestInit = {...options, credentials: 'same-origin'};
        requestOptions.headers = {
            ...requestOptions.headers || {},
            baseURL: window.baseUrl(''),
            'X-CSRF-TOKEN': csrfToken,
        };

        const response = await fetch(requestUrl, requestOptions);
        const content = await this.getResponseContent(response) || '';
        const returnData: FormattedResponse = {
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
     */
    protected async dataRequest(method: string, url: string, data: Record<string, any>|null): Promise<FormattedResponse> {
        const options: RequestInit & RequestOptions = {
            method,
            body: data as BodyInit,
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
        // of request, hence the addition of the magic _method value.
        if (data instanceof FormData && method !== 'post') {
            data.append('_method', method);
            options.method = 'post';
        }

        return this.request(url, options);
    }

    /**
     * Perform a HTTP GET request.
     * Can easily pass query parameters as the second parameter.
     */
    async get(url: string, params: {} = {}): Promise<FormattedResponse> {
        return this.request(url, {
            method: 'GET',
            params,
        });
    }

    /**
     * Perform a HTTP POST request.
     */
    async post(url: string, data: null|Record<string, any> = null): Promise<FormattedResponse> {
        return this.dataRequest('POST', url, data);
    }

    /**
     * Perform a HTTP PUT request.
     */
    async put(url: string, data: null|Record<string, any> = null): Promise<FormattedResponse> {
        return this.dataRequest('PUT', url, data);
    }

    /**
     * Perform a HTTP PATCH request.
     */
    async patch(url: string, data: null|Record<string, any> = null): Promise<FormattedResponse> {
        return this.dataRequest('PATCH', url, data);
    }

    /**
     * Perform a HTTP DELETE request.
     */
    async delete(url: string, data: null|Record<string, any> = null): Promise<FormattedResponse> {
        return this.dataRequest('DELETE', url, data);
    }

    /**
     * Parse the response text for an error response to a user
     * presentable string. Handles a range of errors responses including
     * validation responses & server response text.
     */
    protected formatErrorResponseText(text: string): string {
        const data = text.startsWith('{') ? JSON.parse(text) : {message: text};
        if (!data) {
            return text;
        }

        if (data.message || data.error) {
            return data.message || data.error;
        }

        const values = Object.values(data);
        const isValidation = values.every(val => {
            return Array.isArray(val) && val.every(x => typeof x === 'string');
        });

        if (isValidation) {
            return values.flat().join(' ');
        }

        return text;
    }

}
