from login_box import LoginBox, Receive_data
from os.path import expanduser
from watcher import Watcher
from threading import Thread
from utils import *
import login_box
import os
import time
import sys

reload(sys)
sys.setdefaultencoding("utf-8")

class ClientDaemon:
    def __init__(self):
        self.init_home_dir()
        self.api = "http://apolo.budibox.com/api/" 
        self.login = LoginBox()
        self.watcher = Watcher(self.budibox_home)
        
        
    def init_home_dir(self):
        # Searchs for user home folder and creates budibox folder
        system_enconding = sys.getfilesystemencoding() #mbcs
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
        keep_alive = Thread(target=self.keep_alive, args=())
        sync = Thread(target=self.sync, args=())
        # Start threads
        watcher_thread.start()
        requests_thread.start()
        keep_alive.start()
        sync.start()
        # Join threads
        watcher_thread.join()
        requests_thread.join()
        keep_alive.join()
        sync.join()
    
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
        
    def sync(self):
        while True:
            url = self.api+'files/getUserFiles.php'
            values = {'apikey': '12',
                      'user': login_box.client.get_email() 
                      }
            
            response = json_request(url, values)
            
            if (response['result'] != 'ok'):
                print_message("Error trying to get user files of " + login_box.client.get_email())                
            else:
                print_message("Total files: " + str(len(response['files'])))
                self.restore_file(response['files'])
            time.sleep(60)
            
    def restore_file(self, files):
        list_dir = os.listdir(self.budibox_home)
        for file in files:
            file = file['path'].replace("/", "")
            for f in list_dir:
                if (f == file):
                    print f + " fez match "
        
        
    def keep_alive(self):
        while True:
            url = self.api+'computers/keepAlive.php'
            values = {'apikey': '12',
                      'computerId': self.computerId 
                      }
            
            response = json_request(url, values)
            
            if (response['result'] != 'ok'):
                print_message("Error sending message of keepAlive of computerId " + self.computerId)
            
            time.sleep(10)
        
    def listen_watcher(self):
        self.watcher.start(login_box.client, self.computerId)
        
    def listen_requests(self):
        while(True):
            url = self.api+'requests/getComputerRequests.php'
            values= {'apikey': '12',
                     'computerId': self.computerId
                     }
            response = json_request(url, values)
            if (response['result'] == 'ok'):
                total_requests = len(response['requests'])
                print_message("Requests: " + str(total_requests) )
                if (total_requests > 0):
                    self.handle_request(response['requests'])
            time.sleep(60)

    def handle_request(self, requests):
        for request in requests:
            if (request['action'] == "storeChunk"):
                self.store_chunk(request['chunkNumber'], request['modification'], request['fileId']) 
            if (request['action'] == "deleteFile"):
                self.delete_chunks(request['modification'])
    
    def delete_chunks(self, modification):
        list_dir = os.listdir(self.budibox_home+"/chunks/")
        for file in list_dir:
            if (file.startswith(modification)):
                f = open(self.budibox_home+"/chunks/"+file, "rb")
                file_size = len(f.read())
                f.close()
                os.remove(self.budibox_home+"/chunks/"+file)
                url = self.api+'users/incOfferUsage.php'
                values= {'apikey': '12',
                         'user': login_box.client.get_email(),
                         'value': str(-file_size)
                         }
                response = json_request(url, values)
                
                if (response['result'] == 'ok'):
                    print_message("Decremented offer_usage in " + str(file_size))
                    
                else:
                    print_message("Error decrementing offer_usage in " + str(file_size))
                
                
        url = self.api+'requests/confirmFileDelete.php'
        values= {'apikey': '12',
                 'computerId': self.computerId,
                 'modification': modification
                 }
        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            print_message("Sent confirm delete message of modification: " + modification)
            
        else:
            print_message("Error confirm delete message of modification: " + modification)
                
    
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
            print_message("Error trying to get chunk body of fileID " + fileId['$id'] + "and chunkNumber " + str(chunkNumber))
            return False
        
        print_message("Processing request, getting chunk body of " + fileId['$id'] + " and chunkNumber " + str(chunkNumber))
        
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

        if (response['result'] == 'ok'):
            print_message("Sent confirmation message: fileId " + fileId['$id'] + " and chunkNumber " + str(chunkNumber) + " and computerId " + self.computerId)
            
            # Adds space of the offer_used
            url = self.api+'users/incOfferUsage.php'
            values= {'apikey': '12',
                     'user': login_box.client.get_email(),
                     'value': str(len(chunk_body))
                     }
            response_space = json_request(url, values)
            
            if (response_space['result'] == 'ok'):
                print_message("Increment offer usage successfully with " + str(len(chunk_body)))
                return True
            else:
                print_message("Error in incrementing offer usage with " + str(len(chunk_body)))
                return False
        else:            
            print_message("Error trying to send confirm message: fileId " + fileId['$id'] + " and chunkNumber " + str(chunkNumber) + " and computerId " + self.computerId)
            return False
               

teste = ClientDaemon()
teste.client_start()
