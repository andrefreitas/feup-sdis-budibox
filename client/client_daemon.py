from login_box import LoginBox, Receive_data
from os.path import expanduser
from watcher import Watcher
from threading import Thread
from utils import *
import login_box
import os
import time

class ClientDaemon:
    def __init__(self):
        self.init_home_dir()
        self.api = "http://master.budibox.com/api/"
        self.login = LoginBox()
        self.watcher = Watcher(self.budibox_home)
        
        
    def init_home_dir(self):
        # Searchs for user home folder and creates budibox folder
        self.budibox_home = expanduser("~") + "/budibox"
        
        # Creates budibox folder
        if(not os.path.exists(self.budibox_home)):
            os.makedirs(self.budibox_home)
    
    
    def client_start(self):
        self.login.start()
        self.computerId = self.get_computer_id()
        watcher_thread = Thread(target=self.listen_watcher, args=())
        requests_thread = Thread(target=self.listen_requests, args=())
        watcher_thread.start()
        requests_thread.start()
    
    def get_computer_id(self):
        url = self.api+'computers/getComputerId.php'
        values= {'apikey': '12',
                 'user': login_box.client.get_email(),
                 'computer': get_computer_name()
                 }
        response = json_request(url, values)
        print response
        
        if (response['result'] == 'ok'):
            return response['computerId']
        
    def listen_watcher(self):
        self.watcher.start(login_box.client)
        
    def listen_requests(self):
        while(True):
            print "Handling requests to backup..."
            url = self.api+'requests/getComputerRequests.php'
            values= {'apikey': '12',
                     'computerId': self.computerId
                     }
            response = json_request(url, values)
            if (response['result'] == 'ok'):
                if (len(response['requests']) > 0):
                    self.handle_request(response['requests'])
            time.sleep(60)

    def handle_request(self, requests):
        print len(requests)

teste = ClientDaemon()
teste.client_start()
        
        
        
        