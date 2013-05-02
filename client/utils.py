"""
 Utils
 Authors: Ana Gomes, Andre Freitas
 Description: This is a class with some very useful utilities. 
"""

import httplib
import urllib
import urllib2
import json
import platform
import socket
import re

def json_request(url,params):
    url += "?"
    for param in params.keys():
        url += param + "=" + params[param] + "&"
    return json.load(urllib.urlopen(url))

def get_computer_name():
    return platform.node()

def get_location(ip_address):
    url = 'http://api.hostip.info/get_html.php?ip=' + ip_address + '&position=true'
    response = urllib.urlopen(url).read()
    
    latitude_exp = "(Latitude:).(-?[0-9]+\.+[0-9]+)"
    latitude = float (re.search(latitude_exp, response).group(2))
    longitude_exp = "(Longitude:).(-?[0-9]+\.+[0-9]+)"
    longitude = float (re.search(longitude_exp, response).group(2))
    
    location = {'latitude': latitude,
                'longitude': longitude
                }
    return json.dumps(location)
    
print get_location('193.136.33.133')
    
