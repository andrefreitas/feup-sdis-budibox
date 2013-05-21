#!/usr/bin/env python

import os, sys, time
from file import File
from utils import *

class Watcher:
    
    def __init__(self, path_to_watch):
        print "Watching " + path_to_watch
        self.api = "http://apolo.budibox.com/api/"
        self.path_to_watch = path_to_watch.replace("\\", "/")
        
    def start(self, client):
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
        file_extension_pattern=self.path_to_watch+"/chunks/"
        
        # Checks if folder is chunks, only if it isn't it will send modifications
        if (re.search(file_extension_pattern,path) == None):
            # Prints message of file added
            print "Added: " + path
            
            if (os.path.isfile(path)):

                # Creates file information
                f = File(path, client)
                f.generate_file_id()
                
                # Gets relative_path location
                relative_path = path.split(self.path_to_watch)[1].replace("\\", "/")
                
                # Sends request to server with information about the new file
                url = self.api+'files/create.php'
                values = {'apikey': '12',
                          'path': relative_path,
                          'user': client.get_email(),
                          'modification': f.get_file_id()
                          }
                
                response = json_request(url, values)
                
                if (response['result'] != 'ok'):
                    print "Error sending information about file!"
                    return
                    
                print response
                
                url = self.api+'files/getId.php'
                values = {'apikey': '12',
                          'path': relative_path,
                          'user': client.get_email(),
                          }
                
                response = json_request(url, values)
                
                if (response['result'] != 'ok'):
                    print "Error getting fileId!"
                    return
                
                # Send information about chunks to server
                db_file_id = response['id']
                f.generate_chunks(db_file_id)
            
    def removed(self, path, client):
        # Gets relative_path location
        relative_path = path.split(self.path_to_watch)[1].replace("\\", "/")
        
        url = self.api+'files/setStatus.php'
        values = {'apikey': '12',
                  'path': relative_path,
                  'user': client.get_email(),
                  'status': 'deleted'
                  }
        
        response = json_request(url, values)
        print response
        print "Removed " +  path
        
    def modified(self, path, client):
        path = path.replace("\\", "/")
        file_extension_pattern=self.path_to_watch+"/chunks/"
        
        # Checks if folder is chunks, only if it isn't it will send notifications
        if (re.search(file_extension_pattern,path) != None):
            print "Removed Modified " + path
        else:   
            if (os.path.isfile(path)):
                # Creates file information
                f = File(path, client)
                f.generate_file_id()
                
                # Gets relative_path location
                relative_path = path.split(self.path_to_watch)[1].replace("\\", "/")
                
                # Sends request to server with information about the new file
                url = self.api+'files/modify.php'
                values = {'apikey': '12',
                          'path': relative_path,
                          'user': client.get_email(),
                          'modification': f.get_file_id()
                          }
                
                response = json_request(url, values)
                
                if (response['result'] != 'ok'):
                    print "Error sending information about file!"
                    return
                    
                print response
                
                url = self.api+'files/getId.php'
                values = {'apikey': '12',
                          'path': relative_path,
                          'user': client.get_email(),
                          }
                
                response = json_request(url, values)
                
                if (response['result'] != 'ok'):
                    print "Error getting fileId!"
                    return
                
                # Send information about chunks to server
                db_file_id = response['id']
                f.generate_chunks(db_file_id)
                print "Modified " +  path
        
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