import sys
sys.path.append('/usr/local/lib/python3.9/site-packages')
#sys only for me on the macOS fixed boto3 not found

import boto3
import time

# use commandline: "aws configure" setting default region, keys, etc
client = boto3.client('logs')

query = 'darhmedia@';

response = client.filter_log_events(
    logGroupName='cb-gw01',
    logStreamNames=['mail.log'],
    filterPattern='[message=*'+query+'*]'
)

if len(response['events']) > 0:
    for x in response['events']:
        tm = str(x['timestamp'])[0:-3]
        back = time.strftime("%Y-%m-%d %H:%M", time.localtime(int(tm)-3600))+"\n"+x['message']+"\n\n"
        print(back)
else:
    print('no records found or try again, because sometimes cloudwatch events array is empty')
