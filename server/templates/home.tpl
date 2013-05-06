<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to Budibox</title>
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="shortcut icon" href="images/favicon.ico" />
        <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
        <script src="javascript/scripts.js"></script>
        <meta charset="UTF-8" />
    </head>
    <body>
        <div id="header">
            <div class="container">
                <div class="login">
                    <form action="actions/login.php" onsubmit="return validateLogin()">
                        <input type="email" name="email" placeholder="Email" />
                        <input type="password" name="password" placeholder="Password"/>
                        <input type="submit" value="LOGIN">                     
                    </form>
                     <span class="forgot">Don't remember your password?</span>
                </div>
             </div>
        </div>
        <div class="container">
            <div class="subnotifications"></div>
            <img src="images/clouds.png">
            <div class="notifications">
               {if isset($welcome) and $welcome eq 0} 
                 <div class="error">
                     Invalid registration key!
                 </div>
                {/if}
                {if isset($welcome) and $welcome eq 1} 
                 <div class="confirmation">
                  Your registration has been confirmed :) 
                 </div>
                {/if} 
                {if isset($welcome) and $welcome eq 2} 
                 <div class="confirmation">
                      That account was already active.
                 </div>
                {/if}
            </div>
  
            <div class="blocks">
                <div class="block unlimited">
                    <h1>Unlimited cloud space!</h1>
                    <img src="images/storage.png" alt="Storage disks">
                    We offer you unlimited space in our cloud. The more you offer the more you have for yourself!
                </div>
                <div class="block register">
                    <h1>Register</h1>
                    <form>
                        <input type="text" name="name" placeholder="Name"/> <br/>
                        <input type="email" name="email" placeholder="Email"/> <br/>
                        <input type="password" name="password1" placeholder="Password"/> <br/>
                        <input type="password" name="password2" placeholder="Confirm Password"/> <br/>
                        <button type="button" name="register">Confirm</button>
                    </form>
                </div>
                <div class="block download">
                    <h1>Download the app</h1>
                     We love all plataforms so choose yours:
                     <span>
                     <img src="images/windows.png" width="80" height="80" alt="Windows">
                     <img src="images/osx.png" width="80" height="80" alt="OSX">
                     <img src="images/linux.png" width="80" height="80" alt="Linux">
                     </span>
                </div>
            </div>
        </div>
   
    </body>
</html>