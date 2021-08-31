<h1 class="list-heading text-capitals mb-l">Getting Started</h1>

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
<p>The API is primarily design to be interfaced using JSON so the majority of API endpoints, that accept data, will read JSON request data although <code>application/x-www-form-urlencoded</code> request data is also accepted. Endpoints that receive file data will need data sent in a <code>multipart/form-data</code> format although this will be highlighted in the documentation for such endpoints.</p>
<p>For endpoints in this documentation that accept data, a "Body Parameters" table will be available showing the parameters that will accepted in the request. Any rules for the values of such parameters, such as the data-type or if they're required, will be shown alongside the parameter name.</p>

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
        <th>Parameter</th>
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