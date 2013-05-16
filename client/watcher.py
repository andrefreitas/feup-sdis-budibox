#!/usr/bin/env python

import os, sys, time
from file import File

class Watcher:
    
    def __init__(self, path_to_watch):
        print "Watching " + path_to_watch
        
        self.path = path_to_watch
        
    def start(self, client):
        before = self.files_to_timestamp(self.path)
        
        while 1:
            time.sleep (1)
            after = self.files_to_timestamp(self.path)
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
                   self.added(path)
                
            if removed:
                 for path in removed:
                   self.removed(path)
                   
            if modified:
                for path in modified:
                    self.modified(path)
    
            before = after
        
    def added(self, path):
        print "Added: " + path
        
    def removed(self, path):
        print "Removed " + path
        
    def modified(self, path):
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