<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @onfidoAssets

    <title>Onfido verification</title>

    <style>
        *, *::before, *::after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            width: 100vw;
        }

        main {
            width: 100%;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <main>
        <section>
            <div id="onfido-mount"></div>
        </section>
    </main>

    @onfidoScripts

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var sdkToken = @js($sdkToken);
            var workflowRunId = @js($workflowRunId);
            window.$onfido.init('onfido-mount', sdkToken, workflowRunId);
        });
    </script>
</body>
</html>
