<!doctype html>
<html>
<head>
    <title>{!! $metadata['title'] !!}</title>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"/>
    <style>
        body {
            margin: 0;
        }
    </style>
</head>
<body>

<script
    id="api-reference"
    @foreach($htmlAttributes as $attribute => $value)
        {!! $attribute !!}='{!! $value !!}'
    @endforeach
    data-url="{!! $metadata['openapi_spec_url'] !!}">
</script>


@if($autoLogin)
 <script>
     (function() {
         const key_path = "{{ $keyPath }}"
         const apiReferenceElement = document.getElementById("api-reference");
         const configuration = apiReferenceElement.dataset.configuration
             ? JSON.parse(apiReferenceElement.dataset.configuration)
             : {};

         let existingConfig = configuration;

         const token = localStorage.getItem("scalarAuthToken");
         console.log(token);
         const updatedConfig = {
             ...existingConfig,
             authentication: {
                 preferredSecurityScheme: "bearerAuth",
                 apiKey: {
                     token: token,
                 },
             },
         };
         if (apiReferenceElement) {
             apiReferenceElement.dataset.configuration = JSON.stringify(updatedConfig);
         } else {
             console.error("❌ Element with ID 'api-reference' not found.");
         }
         const originalFetch = window.fetch;
         window.fetch = async (...args) => {
             try {
                 const response = await originalFetch(...args);
                 const clonedResponse = response.clone();

                 if (clonedResponse.headers.get("content-type")?.includes("application/json")) {
                        const data = await clonedResponse.json();

                        const authToken = key_path.split('.').reduce((o, k) => (o || {})[k], data);

                        if (authToken) {
                            localStorage.setItem("scalarAuthToken", `Bearer ${authToken}`);
                            reloadData();
                        }

                    }
                 return response;
             } catch (error) {
                 console.error("❌ Fetch API Interceptor Error:", error);
                 return Promise.reject(error);
             }
         };

         async function reloadData() {
             location.reload();
         }

     })();
 </script>
@endif
<script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
</body>
</html>
