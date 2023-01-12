<h1 class="list-heading text-capitals mb-l">Getting Started</h1>

<p class="mb-none">
    This documentation covers use of the REST API. <br>
    Examples of API usage, in a variety of programming languages, can be found in the <a href="https://github.com/BookStackApp/api-scripts" target="_blank" rel="noopener noreferrer">BookStack api-scripts repo on GitHub</a>.

    <br> <br>
    Some alternative options for extension and customization can be found below:
</p>

<ul>
    <li>
        <a href="{{ url('/settings/webhooks') }}" target="_blank" rel="noopener noreferrer">Webhooks</a> -
        HTTP POST calls upon events occurring in BookStack.
    </li>
    <li>
        <a href="https://github.com/BookStackApp/BookStack/blob/master/dev/docs/visual-theme-system.md" target="_blank" rel="noopener noreferrer">Visual Theme System</a> -
        Methods to override views, translations and icons within BookStack.
    </li>
    <li>
        <a href="https://github.com/BookStackApp/BookStack/blob/master/dev/docs/logical-theme-system.md" target="_blank" rel="noopener noreferrer">Logical Theme System</a> -
        Methods to extend back-end functionality within BookStack.
    </li>
</ul>

<hr>

<h5 id="authentication" class="text-mono mb-m">Authentication</h5>
<p>
    To access the API a user has to have the <em>"Access System API"</em> permission enabled on one of their assigned roles.
    Permissions to content accessed via the API is limited by the roles & permissions assigned to the user that's used to access the API.
</p>
<p>Authentication to use the API is primarily done using API Tokens. Once the <em>"Access System API"</em> permission has been assigned to a user, a "API Tokens" section should be visible when editing their user profile. Choose "Create Token" and enter an appropriate name and expiry date, relevant for your API usage then press "Save". A "Token ID" and "Token Secret" will be immediately displayed. These values should be used as a header in API HTTP requests in the following format:</p>
<pre><code class="language-css">Authorization: Token &lt;token_id&gt;:&lt;token_secret&gt;</code></pre>
<p>Here's an example of an authorized cURL request to list books in the system:</p>
<pre><code class="language-shell">curl --request GET \
  --url https://example.com/api/books \
  --header 'Authorization: Token C6mdvEQTGnebsmVn3sFNeeuelGEBjyQp:NOvD3VlzuSVuBPNaf1xWHmy7nIRlaj22'</code></pre>
<p>If already logged into the system within the browser, via a user account with permission to access the API, the system will also accept an existing session meaning you can browse API endpoints directly in the browser or use the browser devtools to play with the API.</p>

<hr>

<h5 id="request-format" class="text-mono mb-m">Request Format</h5>

<p>
    For endpoints in this documentation that accept data a "Body Parameters" table will be available to show the parameters that are accepted in the request.
    Any rules for the values of such parameters, such as the data-type or if they're required, will be shown alongside the parameter name.
</p>

<p>
    The API can accept request data in the following <code>Content-Type</code> formats:
</p>

<ul>
    <li>application/json</li>
    <li>application/x-www-form-urlencoded*</li>
    <li>multipart/form-data*</li>
</ul>

<p>
    <em>
        * Form requests currently only work for POST requests due to how PHP handles request data.
        If you need to use these formats for PUT or DELETE requests you can work around this limitation by
        using a POST request and providing a "_method" parameter with the value equal to
        <code>PUT</code> or <code>DELETE</code>.
    </em>
</p>

<p>
    Regardless of format chosen, ensure you set a <code>Content-Type</code> header on requests so that the system can correctly parse your request data.
    The API is primarily designed to be interfaced using JSON, since responses are always in JSON format, hence examples in this documentation will be shown as JSON.
    Some endpoints, such as those that receive file data, may require the use of <code>multipart/form-data</code>. This will be mentioned within the description for such endpoints.
</p>

<p>
    Some data may be expected in a more complex nested structure such as a nested object or array.
    These can be sent in non-JSON request formats using square brackets to denote index keys or property names.
    Below is an example of a JSON request body data and it's equivalent x-www-form-urlencoded representation.
</p>

<p><strong>JSON</strong></p>

<pre><code class="language-json">{
  "name": "My new item",
  "books": [105, 263],
  "tags": [{"name": "Tag Name", "value": "Tag Value"}],
}</code></pre>

<p><strong>x-www-form-urlencoded</strong></p>

<pre><code class="language-text">name=My%20new%20item&books%5B0%5D=105&books%5B1%5D=263&tags%5B0%5D%5Bname%5D=Tag%20Name&tags%5B0%5D%5Bvalue%5D=Tag%20Value</code></pre>

<p><strong>x-www-form-urlencoded (Decoded for readability)</strong></p>

<pre><code class="language-text">name=My new item
books[0]=105
books[1]=263
tags[0][name]=Tag Name
tags[0][value]=Tag Value</code></pre>

<hr>

<h5 id="listing-endpoints" class="text-mono mb-m">Listing Endpoints</h5>
<p>Some endpoints will return a list of data models. These endpoints will return an array of the model data under a <code>data</code> property along with a numeric <code>total</code> property to indicate the total number of records found for the query within the system. Here's an example of a listing response:</p>
<pre><code class="language-json">{
  "data": [
    {
      "id": 1,
      "name": "BookStack User Guide",
      "slug": "bookstack-user-guide",
      "description": "This is a general guide on using BookStack on a day-to-day basis.",
      "created_at": "2019-05-05 21:48:46",
      "updated_at": "2019-12-11 20:57:31",
      "created_by": 1,
      "updated_by": 1,
      "image_id": 3
    }
  ],
  "total": 16
}</code></pre>
<p>
    There are a number of standard URL parameters that can be supplied to manipulate and page through the results returned from a listing endpoint:
</p>
<table class="table">
    <tr>
        <th width="110">Parameter</th>
        <th>Details</th>
        <th width="30%">Examples</th>
    </tr>
    <tr>
        <td>count</td>
        <td>
            Specify how many records will be returned in the response. <br>
            (Default: {{ config('api.default_item_count') }}, Max: {{ config('api.max_item_count') }})
        </td>
        <td>Limit the count to 50<br><code>?count=50</code></td>
    </tr>
    <tr>
        <td>offset</td>
        <td>
            Specify how many records to skip over in the response. <br>
            (Default: 0)
        </td>
        <td>Skip over the first 100 records<br><code>?offset=100</code></td>
    </tr>
    <tr>
        <td>sort</td>
        <td>
            Specify what field is used to sort the data and the direction of the sort (Ascending or Descending).<br>
            Value is the name of a field, A <code>+</code> or <code>-</code> prefix dictates ordering. <br>
            Direction defaults to ascending. <br>
            Can use most fields shown in the response.
        </td>
        <td>
            Sort by name ascending<br><code>?sort=+name</code> <br> <br>
            Sort by "Created At" date descending<br><code>?sort=-created_at</code>
        </td>
    </tr>
    <tr>
        <td>filter[&lt;field&gt;]</td>
        <td>
            Specify a filter to be applied to the query. Can use most fields shown in the response. <br>
            By default a filter will apply a "where equals" query but the below operations are available using the format filter[&lt;field&gt;:&lt;operation&gt;] <br>
            <table>
                <tr>
                    <td>eq</td>
                    <td>Where <code>&lt;field&gt;</code> equals the filter value.</td>
                </tr>
                <tr>
                    <td>ne</td>
                    <td>Where <code>&lt;field&gt;</code> does not equal the filter value.</td>
                </tr>
                <tr>
                    <td>gt</td>
                    <td>Where <code>&lt;field&gt;</code> is greater than the filter value.</td>
                </tr>
                <tr>
                    <td>lt</td>
                    <td>Where <code>&lt;field&gt;</code> is less than the filter value.</td>
                </tr>
                <tr>
                    <td>gte</td>
                    <td>Where <code>&lt;field&gt;</code> is greater than or equal to the filter value.</td>
                </tr>
                <tr>
                    <td>lte</td>
                    <td>Where <code>&lt;field&gt;</code> is less than or equal to the filter value.</td>
                </tr>
                <tr>
                    <td>like</td>
                    <td>
                        Where <code>&lt;field&gt;</code> is "like" the filter value. <br>
                        <code>%</code> symbols can be used as wildcards.
                    </td>
                </tr>
            </table>
        </td>
        <td>
            Filter where id is 5: <br><code>?filter[id]=5</code><br><br>
            Filter where id is not 5: <br><code>?filter[id:ne]=5</code><br><br>
            Filter where name contains "cat": <br><code>?filter[name:like]=%cat%</code><br><br>
            Filter where created after 2020-01-01: <br><code>?filter[created_at:gt]=2020-01-01</code>
        </td>
    </tr>
</table>

<hr>

<h5 id="error-handling" class="text-mono mb-m">Error Handling</h5>
<p>
    Successful responses will return a 200 or 204 HTTP response code. Errors will return a 4xx or a 5xx HTTP response code depending on the type of error. Errors follow a standard format as shown below. The message provided may be translated depending on the configured language of the system in addition to the API users' language preference. The code provided in the JSON response will match the HTTP response code.
</p>

<pre><code class="language-json">{
	"error": {
		"code": 401,
		"message": "No authorization token found on the request"
	}
}
</code></pre>

<hr>

<h5 id="rate-limits" class="text-mono mb-m">Rate Limits</h5>
<p>
    The API has built-in per-user rate-limiting to prevent potential abuse using the API.
    By default, this is set to 180 requests per minute but this can be changed by an administrator
    by setting an "API_REQUESTS_PER_MIN" .env option like so:
</p>

<pre><code class="language-bash"># The number of API requests that can be made per minute by a single user.
API_REQUESTS_PER_MIN=180</code></pre>

<p>
    When the limit is reached you will receive a 429 "Too Many Attempts." error response.
    It's generally good practice to limit requests made from your API client, where possible, to avoid
    affecting normal use of the system caused by over-consuming system resources.
    Keep in mind there may be other rate-limiting factors such as web-server & firewall controls.
</p>

<hr>

<h5 id="content-security" class="text-mono mb-m">Content Security</h5>
<p>
    Many of the available endpoints will return content that has been provided by user input.
    Some of this content may be provided in a certain data-format (Such as HTML or Markdown for page content).
    Such content is not guaranteed to be safe so keep security in mind when dealing with such user-input.
    In some cases, the system will apply some filtering to content in an attempt to prevent certain vulnerabilities, but
    this is not assured to be a bullet-proof defence.
</p>
<p>
    Within its own interfaces, unless disabled, the system makes use of Content Security Policy (CSP) rules to heavily negate
    cross-site scripting vulnerabilities from user content. If displaying user content externally, it's advised you
    also use defences such as CSP or the disabling of JavaScript completely.
</p>