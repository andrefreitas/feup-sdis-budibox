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
import requests
from datetime import datetime

def json_request(url, payload = {}):
    r = requests.get(url, params=payload)
    try:
        json_answer = r.json()
        return json_answer
    except:
        print "Failed JSON Request"
        return {"result":"error"}

def get_computer_name():
    return platform.node()

def get_location():
    data =  json_request('http://freegeoip.net/json/')
    return (data['latitude'], data['longitude'])

def json_post_request(url, params= {}):
    r = requests.post(url, data=params)
    return r.json()

def print_message(message):
    print  "[" + datetime.now().strftime("%d/%m/%y %H:%M") + "] " + message
