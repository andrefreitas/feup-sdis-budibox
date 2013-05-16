from login_box import LoginBox, Receive_data
from os.path import expanduser
from watcher import Watcher
import login_box
import os

# Makes login if necessary
login = LoginBox()
login.start()

# Searchs for user home folder
budibox_home = expanduser("~") + "/budibox"

# Creates budibox folder
if(not os.path.exists(budibox_home)):
    os.makedirs(budibox_home)

# Starts supervising folder 
w = Watcher(budibox_home)
w.start(login_box.client)