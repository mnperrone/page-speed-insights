<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google PageSpeed Insights</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], select, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="checkbox"] {
            margin-right: 8px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Google PageSpeed Insights</h1>

    <form id="metricsForm">
        <label for="url">URL:</label>
        <input type="url" id="url" name="url" placeholder="https://example.com" required>
        <div id="urlError" class="error" style="display: none;">Please enter a valid URL.</div>

        <div>
            <label>Categories:</label>
            @foreach (['ACCESSIBILITY', 'BEST_PRACTICES', 'PERFORMANCE', 'PWA', 'SEO'] as $category)
                <div>
                    <input type="checkbox" name="categories[]" value="{{ $category }}" id="{{ $category }}">
                    <label for="{{ $category }}">{{ $category }}</label>
                </div>
            @endforeach
        </div>

        <div>
            <label for="strategy">Strategy:</label>
            <select name="strategy" id="strategy" required>
                <option value="DESKTOP">Desktop</option>
                <option value="MOBILE">Mobile</option>
            </select>
        </div>

        <button type="submit">Get Metrics</button>
    </form>

    <h2>Results:</h2>
    <div id="results"></div>

    <script>
        $('#metricsForm').on('submit', function (e) {
    e.preventDefault();

    const url = $('#url').val();
    const categories = $('input[name="categories[]"]:checked').map(function () { return this.value; }).get();
    const strategy = $('#strategy').val();

    // Validar URL
    const urlRegex = /^(https?:\/\/)?([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,6}(\/[a-zA-Z0-9\-._~:?#%&=+;]*\/?)?$/;
    if (!urlRegex.test(url)) {
        $('#urlError').show();  // Mostrar mensaje de error si la URL no es válida
        return;
    } else {
        $('#urlError').hide();  // Ocultar mensaje de error si la URL es válida
    }

    $.ajax({
        url: '{{ route('getMetrics') }}',
        method: 'POST',
        data: {
            url: url,
            categories: categories,
            strategy: strategy,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            $('#results').html('');
            if (response.success) {
                for (const [category, score] of Object.entries(response.metrics)) {
                    $('#results').append(`<p class="success">${category}: ${score}</p>`);
                }
            } else {
                $('#results').html('<p class="error">Error: ' + response.error + '</p>');
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX request failed: " + error);
            console.log(xhr.responseText);
            $('#results').html('<p class="error">An error occurred while fetching the data: ' + xhr.responseText + '</p>');
        }
    });
});
    </script>
</body>
</html>
