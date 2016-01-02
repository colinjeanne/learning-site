<!doctype html>
<html>
   <head>
      <title>Isaac's Learning Site</title>
      <link rel="stylesheet" href="style.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/es6-shim/0.34.0/es6-shim.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/0.10.1/fetch.min.js"></script>
      <script src="https://apis.google.com/js/platform.js" async defer></script>
      <meta charset="utf-8">
      <meta name="google-signin-client_id" content="<?= getenv('GOOGLE_CLIENT_ID') ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
      <div id="app"></div>
      <script src="main.js"></script>
   </body>
</html>