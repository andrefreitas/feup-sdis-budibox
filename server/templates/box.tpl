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
        <script type="text/javascript" 
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhgL6nowR6e3uTj1RCEaSERFs_cm3tsYA&sensor=false">
        </script>
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
                    <li class="peersi">Peers Location</li>
                    <li class="feedbacki">Give Feedback</li>
                    <li class="logouti">Logout</li>
                </ul>
                <div class="userInfo">
                    <div class="avatar">
                        <img src="images/default-avatar.png" alt="Your avatar"/>
                    </div>
                    <div class="info">
                     
                    </div>
                    <div class="space"> <span>{$spaceUsed} % of {$spaceLimit} GB</span></div>
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
			<form class="giveFeedback">
    			<textarea rows="10" cols="62"></textarea>
                <button>Send Feedback</button>
			</form>
			
		</div>
		
	  <!-- Peers location modal -->
      <div id="peersLocationModal" class="reveal-modal xlarge">
			<h1>Peers location</h1>
			<div id="map-canvas" ></div>
			
			<a class="close-reveal-modal">&#215;</a>
		</div>
		
	<!-- Edit Account -->
      <div id="editAccountModal" class="reveal-modal medium">
			<h1>Edit Account</h1>
			<form class="editAccount" method="post">
			    <input type="text" name="name" placeholder="Your name" value="{$name}"/><br/>
			    <input type="email" name="email" placeholder="Your email" value="{$email}"/><br/>
			    <input type="password" name="password" placeholder="Type a new password"/><br/>
			    <h2>Space Offer</h2>
			    If you offer more space you increase your space limit in the cloud. So, you have unlimited space!<br/><br/>
			    <input type="text" name="offer" placeholder="Offer value in GB" value="{$spaceOffer}"/> GB<br/><br/>
			    <input type="hidden" name="oldEmail" value="{$email}"/>
			    <button>Save Changes</button>
			</form>
			
			<a class="close-reveal-modal">&#215;</a>
	  </div>
    </body>
</html>