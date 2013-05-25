from login_box import LoginBox, Receive_data
from os.path import expanduser
from watcher import Watcher
from threading import Thread
from utils import *
from datetime import datetime
from file import File
import login_box
import os
import time
import sys

# Enconding
reload(sys)
sys.setdefaultencoding("utf-8")
system_enconding = sys.getfilesystemencoding() #mbcs

# Listen requests
listen_requests_interval = 10
sync_files_interval = 20
restore_requests = {}
class ClientDaemon:
    def __init__(self):
        self.init_home_dir()
        self.api = "http://apolo.budibox.com/api/" 
        self.login = LoginBox()
        self.watcher = Watcher(self.budibox_home)
        
        
    def init_home_dir(self):
        # Searchs for user home folder and creates budibox folder
        
        self.budibox_home = expanduser("~") + "/budibox"
        self.home = expanduser("~")
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
            time.sleep(sync_files_interval)
            
    def restore_file(self, files):
        list_dir = os.listdir(self.budibox_home)
        global restore_requests
        for file in files:
            file_path = file['path']
            if os.path.isfile(self.budibox_home+file_path):
                if (file['status'] == 'deleted'):
                    os.remove(self.budibox_home+file_path)
                    break
                
                timestamp = int (file['date_modified']['sec'])
                datetime_request = datetime.fromtimestamp(timestamp)
                datetime_local_file = datetime.fromtimestamp(os.path.getmtime(self.budibox_home+file_path))
                
                difference_times = time.mktime(datetime_request.timetuple()) - time.mktime(datetime_local_file.timetuple()) - 3600
                if (difference_times > 0):
                    if not (file_path in restore_requests):   
                        print_message("More recent " + file_path)
                        url = self.api+'files/restoreFile.php'
                        values = {'apikey': '12',
                                  'computerId': self.computerId,
                                  'modification': file['modification'] 
                                  }
                        
                        response = json_request(url, values)
                        
                        if (response['result'] == 'ok'):
                            print_message("Sent request of restore file of " + file_path)
                            restore_requests[file_path] = False
                            
                        else:
                            print_message("Error sending request of restore file of " + file_path)
                else:
                    print "older or equal"

            else:
                if not (file_path in restore_requests) and file['status'] != 'deleted':
                    url = self.api+'files/restoreFile.php'
                    values = {'apikey': '12',
                              'computerId': self.computerId,
                              'modification': file['modification'] 
                              }
                    
                    response = json_request(url, values)
                    restore_requests[file_path] = False
                    if (response['result'] == 'ok'):
                        print_message("Sent request of restore file of " + file_path)
                        
                    else:
                        print_message("Error sending request of restore file of " + file_path)

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
            time.sleep(listen_requests_interval)

    def handle_request(self, requests):
        for request in requests:
            if (request['action'] == "storeChunk"):
                self.store_chunk(request['chunkNumber'], request['modification'], request['fileId']) 
            if (request['action'] == "deleteFile"):
                self.delete_chunks(request['modification'])
            if (request['action'] == "giveChunk"):
                if (os.path.exists(self.budibox_home+"/chunks/"+request['modification']+"_"+str(request['chunkNumber'])+".chunk")):
                    self.send_chunk_to_restore(request['modification'], request['chunkNumber'], request['owner'])
            if (request['action'] == "recoverChunk"):
                self.store_temp_chunk(request['modification'], request['number'], request['path'])
    
    def store_temp_chunk(self, modification, number, path):
        temp_dir = self.home+"/chunks_restore/temp/"
        if(not os.path.exists(temp_dir)):
            os.makedirs(temp_dir)
        
        # Creates chunk
        temp_dir = temp_dir.decode(system_enconding)
        chunk = open(temp_dir+modification+"_"+str(number)+".chunk", "w")
        
        # Gets chunk body
        url = self.api+'chunks/getRecover.php'
        values= {'apikey': '12',
                 'owner': self.computerId,
                 'number': str(number),
                 'modification': modification
                 }

        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            chunk_body = response['chunk']
            chunk.write(chunk_body)
            chunk.close()
            print_message("Chunk of " + modification + " and number " + str(number) + " writed!")
        else:
            chunk.close()
            print_message("Error getting chunk of " + modification + " and number " + str(number) + " writed!")
            
        # Delete chunk recover
        url = self.api+'chunks/deleteChunkRecover.php'
        values= {'apikey': '12',
                 'owner': self.computerId,
                 'modification': modification,
                 'number': str(number)
                 }

        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            print_message("Deleted chunk recover !")
        
        else:
            print_message("Error deleting chunk recover!")
            return
        
        # Confirm chunk recover 
        url = self.api+'requests/confirmRecoverChunk.php'
        values= {'apikey': '12',
                 'computerId': self.computerId,
                 'modification': modification,
                 'chunkNumber': str(number)
                 }

        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            print_message("Confirmed chunk recover !")
        
        else:
            print_message("Error confirming chunk recover!")
            return
        
        # Checks if received all chunks
        url = self.api+'files/restoreFileIsDone.php'
        values= {'apikey': '12',
                 'computerId': self.computerId,
                 'modification': modification
                 }

        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            if (response['isDone'] == True):
                print_message("Restore file " + path + " is done!")
                f = File(self.budibox_home+path, login_box.client, modification)
                f.restore_file(temp_dir, self.budibox_home+path)
                global restore_requests
                del restore_requests[path]
                print "Feito"
                

    def send_chunk_to_restore(self, modification, number, owner):
        path = self.budibox_home+"/chunks/"+modification+"_"+str(number)+".chunk"
        chunk = open(path, "rb")
        chunk_body = chunk.read()
        url = self.api+'chunks/giveForRestore.php'
        values= {'apikey': '12',
                 'modification': modification,
                 'number': str(number),
                 'body': chunk_body,
                 'owner': owner['$id']
                 }
        
        response = json_post_request(url, values)
        
        if (response['result'] == 'ok'):
            print_message("Sent chunk for restore of " + path)
        else:
            print_message("Error sending chunk for restore of " + path)
    
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
