<!DOCTYPE html>
<html>
    <head>
        <title>Budibox explorer</title>
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/reveal.css"/>
        <link rel="shortcut icon" href="images/favicon.ico" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.6.min.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
        <script src="javascript/scripts.js"></script>
        <script src="javascript/userarea.js"></script>
        <script src="javascript/reveal.js"></script>
        <meta charset="UTF-8" />
    </head>
    <body>
        <div id="header">
            <div class="container">
                <ul class="navigation">
                    <li class="boxi">My Box</li>
                    <li class="accounti">My Account</li>
                    <li class="statsi">Statistics</li>
                    <li class="feedbacki">Give Feedback</li>
                    <li class="logouti">Logout</li>
                </ul>
                <div class="userInfo">
                    <div class="avatar">
                        <img src="images/default-avatar.png" alt="Your avatar"/>
                    </div>
                    <div class="info">
                     
                    </div>
                </div>
             </div>
        </div>
        <div class="container">
            <div class="content">
                <div class="head">
                    <h1>Box</h1> Here you can manage all the files you have in the cloud
                </div>
         
                <div class="items">
                    <div class="path">
                    {$dir}
                    </div>
                    <ul class="active">
                        {foreach from=$directories item=directory}
                            <li class="directory">{$directory}</li>
                        {/foreach}
                        {foreach from=$files item=file}
                            <li class="file">{$file}<div class="actions"></div></li>
                        {/foreach}
                    </ul>
                    
                    {if $deletedFiles}  
                    <h2 class="deletedTitle">Deleted</h2>
                    <ul class="deleted">
                        {foreach from=$deletedFiles item=file}
                            <li class="file">{$file}<div class="actions"></div></li>
                        {/foreach}
                    </ul>
                    {/if}
                    
                    {if $pendingFiles}  
                    <h2 class="pendingTitle">Pending Backup</h2>
                    <ul class="pending">
                        {foreach from=$pendingFiles item=file}
                            <li class="file">{$file}<div class="actions"></div></li>
                        {/foreach}
                    </ul>
                    {/if}
                    
                </div>
            </div>
        </div>
      
      <!-- Give Feedback modal -->
      <div id="giveFeedbackModal" class="reveal-modal">
			<h1>Give feedback</h1>
			<p>We would like to hear from you! Write your suggestions :)</p>
			<a class="close-reveal-modal">&#215;</a>
		</div>
    </body>
</html>