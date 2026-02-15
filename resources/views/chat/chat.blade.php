<!doctype html>
<html lang="1">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,400italic,600italic|Roboto+Slab:400,700"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">


    <title>Simple chat with Groq</title>
    <style>
        /* --- VARIABLES --- */
        :root {
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --bg-input: #f9fafb;
            --color-text: #1f2937;
            --color-label: #6b7280;
            --color-border: #e5e7eb;
            --color-btn: #4f46e5;
            --color-btn-hover: #4338ca;
            --radius: 8px;
            --radius-btn: 20px;
        }

        /* --- LAYOUT --- */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-body);
            padding: 20px;
            font-family: 'Open Sans', sans-serif;
        }

        .container {
            background-color: var(--bg-card);
            width: 660px; /* Wide enough for the 528px textareas */
            padding: 30px;
            border-radius: 16px;
            border: 1px solid #d1d5db;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* --- FORM GROUPS --- */
        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: var(--color-label);
            margin-bottom: 5px;
            text-align: left;
        }

        /* Standard width for Inputs */
        label { width: 400px; }

        /* Wider width for Textarea Labels */
        label.wide-label { width: 528px; }

        /* --- SHARED FORM STYLES (Input & Textarea) --- */
        input, textarea {
            width: 400px;
            border: 1px solid var(--color-border);
            background-color: var(--bg-input);
            padding: 8px 12px;
            font-family: inherit;
            color: var(--color-text);
            box-sizing: border-box;
            transition: border-color 0.2s, background-color 0.2s;

            /* Border Radius Fix: Removes native browser styling */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Specifics for Textareas */
        textarea {
            width: 528px; /* 20% + 10% bigger */
            min-height: 180px;
            font-family: 'Roboto Slab', serif;
            line-height: 1.6;
            resize: vertical;
        }

        /* Focus State */
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--color-btn);
            background-color: #fff;
        }

        /* --- BUTTON --- */
        button {
            height: 40px;
            width: 120px;
            margin-top: 20px;
            align-self: center;
            background-color: var(--color-btn);
            color: white;
            border: none;
            border-radius: var(--radius-btn);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: var(--color-btn-hover);
        }

        /* --- NOTIFICATION --- */
        .notification {
            padding: 10px;
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            text-align: center;
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <!-- Model -->
    <div class="input-group">
        <label for="model-input-id">Enter model:</label>
        <input type="text" name="ai_model" id="model-input-id" value="{{$aiModelDefault}}">
    </div>

    <!-- Role -->
    <div class="input-group">
        <label for="role-input-id">Enter role:</label>
        <input type="text" name="ai_role" id="role-input-id">
    </div>

    <!-- Answers (Wider) -->
    <div class="input-group">
        <label for="answers-textarea-id" class="wide-label">Answers:</label>
        <textarea id="answers-textarea-id" readonly></textarea>
    </div>

    <!-- Questions (Wider) -->
    <div class="input-group">
        <label for="questions-textarea-id" class="wide-label">Your questions:</label>
        <textarea id="questions-textarea-id"></textarea>
    </div>

    <button id="send-button-id">Send</button>
</div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    let getCookie = function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        let result = matches ? decodeURIComponent(matches[1]) : undefined;
        return result;
    };

    $('#send-button-id').on('click', function () {
        const formData = new FormData();
        formData.append('ai_model', $('#model-input-id').val());
        formData.append('ai_role', $('#role-input-id').val());
        formData.append('user_message', $('#questions-textarea-id').val());
        $.ajax({
            headers: {
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                //'Accept': 'application/json'
            },
            url: '{{route('chat.handle-request')}}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                $('#answers-textarea-id').text(data.data.answer);
            },
            error: function (xhr, status, errorThrown) {
                var errorMessage = xhr.responseJSON.message;
                $('#notification')
                    .addClass('notification-error')
                    .text(errorMessage)
                    .fadeOut(6000);
            }
        });
    });
</script>
</html>