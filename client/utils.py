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
    print url
    return json.load(urllib.urlopen(url))

def get_computer_name():
    return platform.node()

def get_location():
    data =  json_request('http://freegeoip.net/json/')
    return (data['latitude'], data['longitude'])
