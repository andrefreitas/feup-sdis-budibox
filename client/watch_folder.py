#!/usr/bin/env python

import os, sys, time

def files_to_timestamp(path):
    files = [os.path.join(path, f) for f in os.listdir(path)]
    i = 0
    for f in os.listdir(path):
        if (os.path.isdir(path+"/"+f) and (not f.startswith('.')) and (not f.startswith('$'))):
            try:
                recursive_files = files_to_timestamp(path+"/"+f)
                for r in recursive_files:
                    files.append(r)
            except WindowsError:
                pass
        i+=1
    return dict ([(f, os.path.getmtime(f)) for f in files])

if __name__ == "__main__":

    path_to_watch = sys.argv[1]
    
    print "Watching " + path_to_watch

    before = files_to_timestamp(path_to_watch)

    while 1:
        time.sleep (2)
        after = files_to_timestamp(path_to_watch)

        added = [f for f in after.keys() if not f in before.keys()]
        removed = [f for f in before.keys() if not f in after.keys()]
        modified = []

        for f in before.keys():
            if not f in removed:
                if os.path.getmtime(f) != before.get(f):
                    modified.append(f)

        if added: print "Added: ", ", ".join(added)
        if removed: print "Removed: ", ", ".join(removed)
        if modified: print "Modified ", ", ".join(modified)

        before = after