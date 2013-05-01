"""
 File
 Authors: Ana Gomes, Andre Freitas
 Description: This is a class to manipulate files, generate chunks and restores.
"""
import re
import hashlib
import os
import datetime
from Crypto.Cipher import ARC4

CHUNK_SIZE=64000

class InvalidPathError:
        pass

def fix_directory_path(directory):
    if(directory!="" and (directory[-1]!="/" or directory[-1]!="\\" )):
        directory=directory+"/"
        return directory 


class File:
    def __init__(self, full_path, key, file_id=None):
        self.set_full_path(full_path)
        self.parse_name()
        self._file_id=file_id
        self.encoder = ARC4.new(key)
        self.decoder = ARC4.new(key)
        
    def parse_name(self):
        file_extension_pattern="[a-zA-Z0-9_\-]+\.[a-zA-Z0-9]+$"
        full_path=self.get_full_path()
        try:
            self.set_name(re.search(file_extension_pattern,full_path).group(0))
        except:
            raise InvalidPathError
            
    def generate_file_id(self):
        full_path=self.get_full_path()
        file_id=hashlib.sha256(full_path).hexdigest()
        self.set_file_id(file_id)
        return file_id
    
    def generate_chunks(self, directory=""):
        f=open(self._full_path, "rb")
        directory=fix_directory_path(directory)
        i=0
        while True:
            chunk = f.read(CHUNK_SIZE)
            if (chunk!=""): 
                chunk_file=open(directory+self._name.split(".")[0]+"_"+str(i)+".chunk", "wb")
                chunk_encrypted= self.encoder.encrypt(chunk)
                chunk_file.write(chunk_encrypted)
                chunk_file.close()
                i+=1
            else:
                break
        f.close()        
        return i
    
    def restore_file(self, chunks_directory, destination_directory,expected_chunks):
        chunks_directory=fix_directory_path(chunks_directory)
        destination_directory=fix_directory_path(destination_directory)
                
        if (os.path.exists(destination_directory+self._name)):
            os.remove(destination_directory+self._name)
                
        restored_file=open(destination_directory+self._name, "ab")
        chunks = self.fetch_chunks_restore(chunks_directory, self._file_id)
        if(not (len(chunks)==expected_chunks)):
                return False
            
        # Write chunks to file
        for i in range(len(chunks)):
                chunk=open(chunks_directory+chunks[i], "rb")
                decrypted_file=self.decoder.decrypt(chunk.read())
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
        
        

test = File('C:\\Users\\Ana Gomes\\Documents\\git\\budibox\\README.txt', "teste")
test.generate_file_id()
print test.get_file_id()
test.generate_chunks('C:\\Users\\Ana Gomes\\Documents\\git\\budibox')
test.restore_file('C:\\Users\\Ana Gomes\\Documents\\git\\budibox', 'C:\\Users\\Ana Gomes\\Documents\\git', 1)
