<?php

// Laravel 5.3+ Controller sample
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use AWS;

public function searchemail(Request $request)
{
    $query = $request->input('query');
    
    try {
            $client = AWS::createClient('CloudWatchLogs',[
                'region' => 'eu-central-1'/*,
                'version' => ''*/ 
            ]);
      
            $filter = '[message=*'.$query.'*]';
      
            $result = $client->filterLogEvents([
                //'endTime' => <integer>,
                //'limit' => <integer>,
                'filterPattern' => $filter,
                'logGroupName' => 'cb-gw01', // REQUIRED
                'logStreamName' => 'mail.log', // REQUIRED
                //'nextToken' => '<string>',
                //'startFromHead' => true || false,
                //'startTime' => <integer>,
            ]);
      
            if($result['@metadata']['statusCode'] == 200)
            {
                if(!empty($result['events']))
                {

                    foreach($result['events'] as &$event)
                    {
                        $event['timestamp'] = date('Y-m-d H:i',substr($event['timestamp'],0,-3)-3600);
                        $event['message'] = str_replace(array('<','>'),array('&lt;','&gt;'),$event['message']);
                    }
                    $logs = []; 
                    foreach($result['events'] as $event)
                    {
                        $logs[$event['timestamp']]['timestamp'] = $event['timestamp'];
                        $logs[$event['timestamp']]['message'] = $event['message'];
                    }

                    krsort($logs); // DESC

                    return response()->json([
                        'data' => json_encode([
                            'logs'  => $logs
                        ])
                    ],200);


                }else
                {
                    // not found, events array empty
                }
            }else
            {
                // error
            }

        }
        catch (\Exception $e) {

            // error
        }

}      
