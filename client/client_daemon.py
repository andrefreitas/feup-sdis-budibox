from login_box import LoginBox, Receive_data
from os.path import expanduser
from watcher import Watcher
from threading import Thread
from utils import *
import login_box
import os
import time
import sys
from datetime import datetime

def print_message(message):
    print  "[" + datetime.now().strftime("%d/%m/%y %H:%M") + "] " + message
    
class ClientDaemon:
    def __init__(self):
        self.init_home_dir()
        self.api = "http://apolo.budibox.com/api/" 
        self.login = LoginBox()
        self.watcher = Watcher(self.budibox_home)
        
        
    def init_home_dir(self):
        # Searchs for user home folder and creates budibox folder
        system_enconding = sys.getfilesystemencoding()
        self.budibox_home = expanduser("~") + "/budibox"
        self.budibox_home = self.budibox_home.decode(system_enconding)
        
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
        print_message("Getting the computer ID")
        
        if (response['result'] == 'ok'):
            return response['computerId']
        
    def listen_watcher(self):
        self.watcher.start(login_box.client)
        
    def listen_requests(self):
        while(True):
            
            url = self.api+'requests/getComputerRequests.php'
            values= {'apikey': '12',
                     'computerId': self.computerId
                     }
            
            print_message("Fetching requests")
            response = json_request(url, values)
            
            print response
            
            if (response['result'] == 'ok'):
                if (len(response['requests']) > 0):
                    self.handle_request(response['requests'])
            time.sleep(60)

    def handle_request(self, requests):
        for request in requests:
            if (request['action'] == "storeChunk"):
                self.store_chunk(request['chunkNumber'], request['modification'], request['fileId']) 
            if (request['action'] == "deleteFile"):
                self.delete_chunks(request['modification'])
    
    def delete_chunks(self, modification):
        print modification
        list_dir = os.listdir(self.budibox_home+"/chunks/")
        for file in list_dir:
            if (file.startswith(modification)):
                os.remove(self.budibox_home+"/chunks/"+file)
                
    
    def store_chunk(self, chunkNumber, modification, fileId):
        # Gets Information about chunk to Store
        url = self.api+'chunks/get.php'
        values= {'apikey': '12',
                 'fileId': fileId['$id'],
                 'modification': modification,
                 'number': str(chunkNumber)
                 }
        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            chunk_body = response['chunk']
        else:
            print "Error trying to get chunk body"
            return False
        print response
        
        if(not os.path.exists(self.budibox_home+"/chunks/")):
            os.makedirs(self.budibox_home+"/chunks/")
        chunk_file = open(self.budibox_home+"/chunks/"+modification+"_"+str(chunkNumber)+".chunk", "wb")
        chunk_file.write(chunk_body)
        chunk_file.close()
        
        # Sends confirmStorage message
        url = self.api+'chunks/confirmStorage.php'
        values= {'apikey': '12',
                 'fileId': fileId['$id'],
                 'computerId': self.computerId,
                 'number': str(chunkNumber),
                 'modification': modification
                 }
        response = json_request(url, values)
        print response
        if (response['result'] == 'ok'):
            return True
        else:
            print "Error trying to send confirm message!"
            return False
               

teste = ClientDaemon()
teste.client_start()