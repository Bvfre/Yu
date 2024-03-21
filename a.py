import requests
from bs4 import BeautifulSoup
import requests,random,json
import time,sys,subprocess,re
from datetime import datetime

import requests
import json,os
import time,sys
from requests.adapters import HTTPAdapter
from urllib3.util import Retry
import time,random
import re
from bs4 import BeautifulSoup
from urllib.parse import urlparse
import datetime
import os
import sys
import json
import time
import random
import requests
import datetime
from colorama import Fore, Back, Style, init
from random import randint
import requests
from fake_useragent import UserAgent
import random

# Create an instance of UserAgent
ua = UserAgent()
import os
init(autoreset=True)

sc_ver = "CLAIMBTC.ONLINE v5"

end = "\033[K"
res = Style.RESET_ALL
red = Style.BRIGHT+Fore.RED
bg_red = Back.RED
white = Style.BRIGHT+Fore.WHITE
green = Style.BRIGHT+Fore.GREEN
yellow = Style.BRIGHT+Fore.YELLOW
colors = [Fore.RED, Fore.GREEN, Fore.YELLOW, Fore.BLUE, Fore.MAGENTA, Fore.CYAN]


def wait(x):
    for i in range(x, -1, -1):
        col = yellow if i % 2 == 0 else white
        animation = "⫸" if i % 2 == 0 else "⫸⫸"
        m, s = divmod(i, 60)
        t = f"[00:{m:02}:{s:02}]"
        sys.stdout.write(f"\r  {white}Please wait {col}{t} {animation}{res}{end}\r")
        sys.stdout.flush()
        time.sleep(1)
        
        
def Session():
    session = requests.Session()
    retry = Retry(connect=5, backoff_factor=1)
    adapter = HTTPAdapter(max_retries=retry)
    session.mount('http://', adapter)
    session.mount('https://', adapter)
    return session

class Api_MB:
    def __init__(self):
        self.url = "http://api.multibot.in"
        self.key = "5Z4sWvudz6Jjfw2nK10YcOFkRbQVMU"
        self.max_wait = 300
        self.sleep = 5

    def in_api(self, data):
        session = Session()
        params = {"key": (None, self.key), "json": (None, "1")}
        for key in data:
            params[key] = (None, data[key])
        return session.post(self.url + '/in.php', files=params, verify=False, timeout=5)

    def res_api(self, api_id):
        session = Session()
        params = {"key": self.key, "id": api_id, "json": "1"}
        return session.get(self.url + '/res.php', params=params, verify=False, timeout=5)

    def run(self, data):
        get_in = self.in_api(data)
        if get_in:
            if json.loads(get_in.text)['status']:
                api_id = json.loads(get_in.text)['request']
            else:
                return json.loads(get_in.text)['request']
        else:
            return "WRONG_RESPONSE_API"
        for i in range(self.max_wait//self.sleep):
            time.sleep(self.sleep)
            get_res = self.res_api(api_id)
            if get_res:
                answer = json.loads(get_res.text)['request']
                if 'CAPCHA_NOT_READY' in answer:
                    continue
                else:
                    return answer
                    
                    

def msg_line():
 #   green = "\033[92m"  # ANSI escape code for green color
    print(f"{green}{'━' * 50}")
    

url = 'https://bnbfree.in'

ua = UserAgent()

# Define the login headers with a random User-Agent
login_headers = {
    'Host': 'bnbfree.in',
    'Accept': '*/*',
    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
    'User-Agent': ua.random,  # Use ua.random to get a random User-Agent string
    'X-Requested-With': 'XMLHttpRequest'
}


login_payload = {
    'csrf_token': '',
    'op': 'login_new',
    'btc_address': 'aebebas877@gmail.com',
    'password': '@yudhistira@',
    'tfa_code': ''
}

api = Api_MB()
data = {"method": "hcaptcha", "pageurl": "https://bnbfree.in/", "sitekey": "2ca356f0-8121-44d8-9596-6aeb24529e95"}

with requests.Session() as session:
    # Perform login
    login_response = session.post(url, data=login_payload, headers=login_headers)

    # Check if the login was successful
    if login_response.status_code == 200:
        print("Login successful")
    else:
        print("Login failed")
        sys.exit(1)  # Exit the script if login fails

    # Continue with other actions in a while True loop
    while True:
        # Perform other actions using the session
        # Example: Make a POST request after logging in
        cap = api.run(data)
        claim_payload = f"csrf_token=&op=free_play&fingerprint=1fad3fde0dc3279a86f55e445c824847&client_seed=iEjoup0bY4chRj8p&fingerprint2=1760875237&pwc=0&h_recaptcha_response={cap}"
        response = session.post(url, data=claim_payload,headers=login_headers)

        # Check if the request was successful
        if response.status_code == 200:
            # Extract the desired values from the response
            s = str(response.text)
            parts = s.split(":")
            if len(parts) >= 4:  # Ensure there are enough parts to extract
                extracted_substring = ":".join(parts[1:4])
                subparts = extracted_substring.split(":")
                a = subparts[0]
                b = subparts[1]
                c = subparts[2]
                print(f"roll number={a},  Balance={b}, Reward={c}")
            else:
                print("Unexpected response format")
        
        else:
            pass

        # Wait for a random time before the next iteration
        wait(random.randint(3600, 3672))
