import httplib
import urllib
import urllib2
import json

def json_request(url,params):
    url += "?"
    for param in params.keys():
        url += param + "=" + params[param] + "&"
    return json.load(urllib.urlopen(url))