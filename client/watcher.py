#!/usr/bin/env python

import os, sys, time
from file import File
from utils import *

class Watcher:
    
    def __init__(self, path_to_watch):
        print_message("Watching " + path_to_watch)
        self.api = "http://apolo.budibox.com/api/"
        self.path_to_watch = path_to_watch.replace("\\", "/")
        
    def start(self, client, computer_id):
        self.computer_id = computer_id
        before = self.files_to_timestamp(self.path_to_watch)
        
        while 1:
            time.sleep (1)
            after = self.files_to_timestamp(self.path_to_watch)
            added = [f for f in after.keys() if not f in before.keys()]
            removed = [f for f in before.keys() if not f in after.keys()]
            modified = []
            for f in before.keys():
                if not f in removed:
                    if os.path.getmtime(f) != before.get(f):
                        modified.append(f)
            # Handle Events
            if added: 
               for path in added:
                   self.added(path, client)
                
            if removed:
                 for path in removed:
                   self.removed(path, client)
                   
            if modified:
                for path in modified:
                    self.modified(path, client)
    
            before = after
        
    def added(self, path, client):
        path = path.replace("\\", "/")
        file_extension_pattern=self.path_to_watch+"/chunks"
        if (re.search("Thumbs.db", path) == None):
            # Checks if folder is chunks, only if it isn't it will send modifications
            if (re.search(file_extension_pattern,path) == None):
                # Prints message of file added
                print_message("Added: " + path)
                
                if (os.path.isfile(path)):
                    # Creates file information
                    f = File(path, client)
                    f.generate_file_id()
                    f.get_salt()
                    file_size = f.get_file_size()

                    # Gets relative_path location
                    relative_path = path.split(self.path_to_watch)[1].replace("\\", "/")
                    modification_date = f.get_modification_date().replace(" ", "T").split(".")[0] +"Z"
                    # Sends request to server with information about the new file
                    url = self.api+'files/create.php'
                    values = {'apikey': '12',
                              'path': relative_path,
                              'user': client.get_email(),
                              'modification': f.get_file_id(),
                              'dateModified': modification_date,
                              'size': str(file_size)
                              }
                    response = json_request(url, values)
                    
                    print values
                    
                    if (response['result'] != 'ok'):
                        print_message("Error sending information created about file " + path)
                        return
                    
                    print_message("Created file " + path + " successfully")
                    
                    url = self.api+'files/getId.php'
                    values = {'apikey': '12',
                              'path': relative_path,
                              'user': client.get_email(),
                              }
                    
                    response = json_request(url, values)
                    
                    if (response['result'] != 'ok'):
                        print_message("Error getting fileId of " + path)
                        return
                    
                    print_message("Get fileId of " + path + "successfully")
                    
                    # Send information about chunks to server
                    db_file_id = response['id']
                    f.generate_chunks(db_file_id)
            
    def removed(self, path, client):
        # Gets relative_path location
        relative_path = path.split(self.path_to_watch)[1].replace("\\", "/")
        path = path.replace("\\", "/")
        file_extension_pattern=self.path_to_watch+"/chunks"
        
        if (re.search(file_extension_pattern,path) != None):
            print_message("Removed " +  path)
            # Send message of chunk deleted
            file_name = path.replace(file_extension_pattern, "")
            modification = file_name.split("_")[0].replace("/", "")
            chunk_number = file_name.split("_")[1].split(".")[0]
            url = self.api+'chunks/deleted.php'
            values = {'apikey': '12',
                      'modification': modification,
                      'number': chunk_number,
                      'computerId': self.computer_id
                      }
            
            response = json_request(url, values)
            
            if (response['result'] == 'ok'):
                print_message("Deleted chunk successfully " + file_name)
                
            else:
                print_message("Error sending delete chunk message of " + file_name)
                

        
        if (re.search("Thumsb.db", path) == None):
            print_message("Removed " +  path)
            # Modify File Status 
            url = self.api+'files/setStatus.php'
            values = {'apikey': '12',
                      'path': relative_path,
                      'user': client.get_email(),
                      'status': 'deleted'
                      }
            
            response = json_request(url, values)
            
            if (response['result'] != 'ok'):
                print_message("Error while trying to change file "+ path +" status")
                return
            
            print_message("Set status of file " + path + " to deleted")
            
        
    def modified(self, path, client):
        path = path.replace("\\", "/")
        file_extension_pattern=self.path_to_watch+"/chunks"

        if (re.search("Thumbs.db", path) == None):
            # Checks if folder is chunks, only if it isn't it will send notifications
            if (re.search(file_extension_pattern,path) != None):
                if (os.path.isfile(path)):
                    print_message("Modified in chunks path: " + path)
                    file_name = path.replace(file_extension_pattern, "")
                    modification = file_name.split("_")[0].replace("/", "")
                    chunk_number = file_name.split("_")[1].split(".")[0]
                    url = self.api+'chunks/deleted.php'
                    values = {'apikey': '12',
                              'modification': modification,
                              'number': chunk_number,
                              'computerId': self.computer_id
                              }
                    
                    response = json_request(url, values)
                    
                    if (response['result'] == 'ok'):
                        print_message("Deleted chunk successfully " + file_name)
                        
                    else:
                        print_message("Error sending delete chunk message of " + file_name)
            else:   
                if (os.path.isfile(path)):
                    print_message("Modified " +  path)
                    # Creates file information
                    f = File(path, client)
                    f.generate_file_id()
                    
                    # Gets relative_path location
                    relative_path = path.split(self.path_to_watch)[1].replace("\\", "/") 
                    modification_date = f.get_modification_date().replace(" ", "T").split(".")[0] +"Z"    
                    # Sends request to server with information about the new file
                    url = self.api+'files/modify.php'
                    values = {'apikey': '12',
                              'path': relative_path,
                              'user': client.get_email(),
                              'modification': f.get_file_id(),
                              'dateModified': modification_date
                              }
                    
                    response = json_request(url, values)
                    
                    if (response['result'] != 'ok'):
                        print_message("Error sending information modify about file " + path)
                        return
                        
                    print_message("Sent modify message of: " + path)
                    
                    url = self.api+'files/getId.php'
                    values = {'apikey': '12',
                              'path': relative_path,
                              'user': client.get_email(),
                              }
                    
                    response = json_request(url, values)
                    
                    if (response['result'] != 'ok'):
                        print_message("Error getting fileId of " + path)
                        return
                    
                    # Send information about chunks to server
                    db_file_id = response['id']
                    f.generate_chunks(db_file_id)
        
    def files_to_timestamp(self, path):
        files = [os.path.join(path, f) for f in os.listdir(path)]
        i = 0
        for f in os.listdir(path):
            if (os.path.isdir(path+"/"+f) and (not f.startswith('.')) and (not f.startswith('$'))):
                try:
                    recursive_files = self.files_to_timestamp(path+"/"+f)
                    for r in recursive_files:
                        files.append(r)
                except WindowsError:
                    pass
            i+=1
        return dict ([(f, os.path.getmtime(f)) for f in files])