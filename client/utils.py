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

def json_request(url,params = {}):
    url += "?"
    for param in params.keys():
        url += param + "=" + params[param] + "&"
    
    return json.load(urllib.urlopen(url))

def get_computer_name():
    return platform.node()

def get_location():
    url = 'http://freegeoip.net/json/'
    response = json.loads(urllib.urlopen(url).read())

    latitude = response['latitude']
    longitude = response['longitude']

    location = {'latitude': latitude,
                'longitude': longitude
                }
    return json.dumps(location)

print get_location()