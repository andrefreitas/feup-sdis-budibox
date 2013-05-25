"""
 File
 Authors: Ana Gomes, Andre Freitas
 Description: This is a class to manipulate files, generate chunks and restores.
"""
import re
import hashlib
import os
import datetime
import base64
import sys
import math
from utils import *

import datetime

BASE64_CHUNK_FULL_SIZE=85388
CHUNK_SIZE=64000

class InvalidPathError:
        pass

def fix_directory_path(directory):
    if(directory!="" and (directory[-1]!="/" or directory[-1]!="\\" )):
        directory=directory+"/"
        return directory 


class File:
    def __init__(self, full_path, client, file_id=None):
        full_path = full_path.replace("\\", "/")
        self.set_full_path(full_path)
        self.parse_name()
        self._file_id=file_id
        self.api = "http://apolo.budibox.com/api/"
        self.client = client
        self.get_salt()
    
    def get_file_size(self):
        original_file_size = int(os.path.getsize(self._full_path))        
        salt_size = len(self.key)
        total_nr_chunks = original_file_size/CHUNK_SIZE
        size_last_chunk = original_file_size%CHUNK_SIZE
        
        one_chunk = math.ceil((CHUNK_SIZE+salt_size)/3)*4
        last_chunk = math.ceil((size_last_chunk+salt_size)/3)*4
        total = BASE64_CHUNK_FULL_SIZE*total_nr_chunks+last_chunk+300
        return total
    
    def get_salt(self):
        url = self.api+'users/getKey.php'
        values = {'apikey': '12',
                  'user': self.client.get_email()
                  }
        
        response = json_request(url, values)
        
        if (response['result'] == 'ok'):
            self.key = response['key']
            print_message("Get salt successfully")
        else:
            print_message("Error in getting salt")
            
    def parse_name(self):
        file_extension_pattern="[a-zA-Z0-9_\-\s]+\.[a-zA-Z0-9\s]+$"
        full_path=self.get_full_path()
        try:
            self.set_name(re.search(file_extension_pattern,full_path).group(0))
        except:
            raise InvalidPathError
    
    def get_modification_date(self):
        full_path=self.get_full_path()
        t = os.path.getmtime(full_path)
        return str(datetime.datetime.fromtimestamp(t))
        
    def generate_file_id(self):
        modification_date=self.get_modification_date()
        full_path=self.get_full_path()
        computer_name=get_computer_name()
        raw = full_path+modification_date+computer_name+self.client.get_email()
        system_enconding = sys.getfilesystemencoding()
        raw = raw.encode(system_enconding)
        file_id=hashlib.sha256(raw).hexdigest()
        self.set_file_id(file_id)
        return file_id

    def generate_chunks(self, db_file_id):
        f=open(self._full_path, "rb")
        i=0
        while True:
            # Parses file to generate chunks to send
            chunk = f.read(CHUNK_SIZE)
            if (chunk!=""): 
                key = "".join(["/x%02x" % ord(char) for char in self.key])
                chunk += key
                chunk_encrypted = base64.b64encode(chunk)
                
                # Creates request for a chunk
                location = get_location()
                url = self.api+'chunks/put.php'
                values = {'apikey': '12',
                          'fileId': db_file_id,
                          'modification': self._file_id,
                          'body': chunk_encrypted,
                          'number': str(i),
                          'lat': str(self.client.get_lat()),
                          'lon': str(self.client.get_lon())
                          }

                response = json_post_request(url, values)
                
                if (response['result'] == 'ok'):
                    print_message("Putchunk successfully of fileId " + db_file_id + " and chunknumber " + str(i))
                else:
                    print_message("Erro in putchunk fileId " + db_file_id + " and chunknumber " + str(i)+" " + response['result'])
                    
                i+=1
            else:
                break
        f.close()        
        return i
    
    
    def restore_file(self, chunks_directory, destination_directory):
        if (os.path.exists(destination_directory)):
            os.remove(destination_directory)

        restored_file=open(destination_directory, "ab")
        chunks = self.fetch_chunks_restore(chunks_directory, self._file_id)
            
        # Write chunks to file
        for i in range(len(chunks)):
                chunk=open(chunks_directory+chunks[i], "r")
                decrypted_file=base64.b64decode(chunk.read())
                key = "".join(["/x%02x" % ord(char) for char in self.key])
                decrypted_file = decrypted_file.replace(key, "")
                
                restored_file.write(decrypted_file)
                chunk.close()
                
        restored_file.close()
        return True
                
    def fetch_chunks_restore(self, path, file_id):
        dir_list = os.listdir(path)
        chunks={}
        chunk_name_pattern = file_id+"_([0-9]+)\.chunk"
        
        # Fetch and sort chunk files
        for file_name in dir_list:
            match = re.search(chunk_name_pattern, file_name)
            if (match):
                chunks[int(match.group(1))] = file_name
                        
        return chunks
 
    def get_relative_path(self):
        return self.relative_path
  
    def set_full_path(self,full_path):
        self._full_path=full_path

    def get_full_path(self):
        return self._full_path
    
    def set_name(self,name):
        self._name=name
    
    def get_name(self):
        return self._name
    
    def set_file_id(self,file_id):
        self._file_id=file_id
    
    def get_file_id(self):
        return self._file_id
        
        


