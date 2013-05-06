<!DOCTYPE html>
<html>
    <head>
        <title>Change password</title>
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="shortcut icon" href="images/favicon.ico" />
        <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
        <script src="javascript/scripts.js"></script>
        <meta charset="UTF-8" />
    </head>
    <body>
        <form class="setNewPassword">
            <h1>Please set a new password</h1>
            <div class="notifications">
            </div>
            <input type="hidden" name="key" value="{$key}"/>
            <input type="password" name="password" placeholder="New password"/>
            <input type="password" name="password2" placeholder="Confirm password"/>
            <button type="button" name="register">Change password</button>
        </form>

    </body>
 </html>