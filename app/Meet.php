<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Auth;
use App\Course;
use App\Drive;
use App\DriveItem;
use App\UserActivity;
use App\Response;
use App\Meeting;

class Meet
{
    //
    public function getAllMeetingRaport($request,$meetingCode,$startTime,$endTime){
        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $meetings_response = $this->getMeetingRaport($request,$meetingCode,$startTime,$endTime,$nextPageToken);
            if($meetings_response->responseCode==200){
                $meetings_info = json_decode($meetings_response->body);
                if(property_exists($meetings_info,'items')){
                    foreach($meetings_info->items as $meeting){
                        $response[] = $meeting;
                    }
                    if(property_exists($meetings_info,'nextPageToken')){
                        $nextPageToken=$meetings_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{
                $state=false;
            }
        }
        return $response;
    }
    public function getMeetingRaport($request,$meetingCode,$startTime,$endTime,$nextPageToken=null){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        $time =  strtotime($endTime) + 3600;
        $newDate = date(DATE_RFC3339,$time);
        $endTime = str_replace("+","%2B",$newDate);

        if($nextPageToken!=null){
            $url = "https://www.googleapis.com/admin/reports/v1/activity/users/all/applications/meet?endTime=".$endTime."&eventName=call_ended&filters=meeting_code==".$meetingCode."&startTime=".$startTime."&pageToken=".$nextPageToken."&key=".$api_key;
        }else{
            $url = "https://www.googleapis.com/admin/reports/v1/activity/users/all/applications/meet?endTime=".$endTime."&eventName=call_ended&filters=meeting_code==".$meetingCode."&startTime=".$startTime."&key=".$api_key;
        }
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }

    public function getMettingsAll($request,$calendarId){
        $response = [];
        $nextPageToken=null;
        $state = true;

        while($state){
            $events_response = $this->getMettings_response($request,$calendarId,$nextPageToken);

            if($events_response->responseCode==200){
                $events_info = json_decode($events_response->body);
                if(property_exists($events_info,'items')){
                    foreach($events_info->items as $event){
                        $response[] = $event;
                    }
                    if(property_exists($events_info,'nextPageToken')){
                        $nextPageToken=$events_info->nextPageToken;
                    }else{
                        $state=false;
                    }
                }else{
                    $state=false;
                }
            }else{

                $state=false;
            }
        }
        return $response;
    }
    public function getMettings_response($request,$calendarId,$nextPageToken=null){
        $api_key = "AIzaSyAMXrfEXn747TY_VujVJHrrfaNufR98ToY";
        $access_code = $request->session()->get('access_token');
        //send post request to google token api to refresh the access token
        if($nextPageToken!=null){
            $url = "https://www.googleapis.com/calendar/v3/calendars/".$calendarId."/events?pageToken=".$nextPageToken."&key=".$api_key;
        }else{
            $url = "https://www.googleapis.com/calendar/v3/calendars/".$calendarId."/events?key=".$api_key;
        }
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_code));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
 
        $response = new Response($httpCode,$head);
        //this is when the token is updated successfully
        return $response;
    }

    public function getMeetings_response($request,$calendarId){
        $api_key = "AIzaSyB0RZFMufhdbZHSXAV_6_NQLwDzWPvjbEw";
        $access_token = $request->session()->get('access_token');
        $curl_2 = curl_init();
        $date = date(DATE_RFC3339);
        $date = str_replace("+","%2B",$date);
        curl_setopt($curl_2 , CURLOPT_URL ,"https://www.googleapis.com/calendar/v3/calendars/".$calendarId."/events?timeMax=".$date."&key=".$api_key);
        curl_setopt($curl_2, CURLOPT_HTTPHEADER, array('Accept: application/json','Authorization: Bearer '.$access_token));
        curl_setopt($curl_2, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl_2);
        $httpCode = curl_getinfo($curl_2, CURLINFO_HTTP_CODE);
        curl_close($curl_2);
        $reponse = new Response($httpCode,$head);
        return $reponse;
    }

    public function getMeetings($request,$courseId){
        $course_api = new Course($request);
        $events = [];
        $course_response = $course_api->getCourse($courseId);
        if($course_response->responseCode==200){
            $course = json_decode($course_response->body);
            $calendarId ="";
            if(property_exists($course,'calendarId')){
                $calendarId = $course->calendarId;
            }
            

            // for testing i will use my own calender 
            // "abdelkarim.chamlal@uae.ac.ma"
            
            $events_response = $this->getMeetings_response($request,$calendarId);
            if($events_response->responseCode==200){

                $events_info = json_decode($events_response->body);
                if(property_exists($events_info,'items')){
                    for($i = 0 ; $i < count ($events_info->items);$i++){
                        $event = $events_info->items[$i];
                        $meetEvent = new MeetEvent();

                        $meetEvent->event_start_time = $event->start->dateTime;

                        $meetEvent->event_end_time = $event->end->dateTime;


                        if(property_exists($event,'summary'))
                        $meetEvent->event_name = $event->summary;

                        if(property_exists($event,'conferenceData')){
                            $conferenceData = $event->conferenceData;
                            $meetEvent->event_id = $conferenceData->conferenceId;
                        }
                        $events[] = $meetEvent;
                    }
                }
            }
        }
        return $events;
    }

    public function getUserActivities_response($request,$email,$event){
        $api_key = "AIzaSyB0RZFMufhdbZHSXAV_6_NQLwDzWPvjbEw";
        $access_token = $request->session()->get('access_token');
        $event_id = str_replace("-","",$event->event_id);
        $curl_2 = curl_init();
        curl_setopt($curl_2 , CURLOPT_URL ,"https://www.googleapis.com/admin/reports/v1/activity/users/".$email."/applications/meet?filters=meeting_code==".$event_id."&startTime=".str_replace("+","%2B",$event->event_start_time)."&endTime=".str_replace("+","%2B",$event->event_end_time)."&eventName=call_ended&key=".$api_key);
        curl_setopt($curl_2, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_token));
        curl_setopt($curl_2, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl_2);
        $httpCode = curl_getinfo($curl_2, CURLINFO_HTTP_CODE);
        curl_close($curl_2);
        $reponse = new Response($httpCode,$head);
        return $reponse;
    }

    public function getUserActivities($request,$courseId,$email){
        $events = $this->getMeetings($request,$courseId);
        $activities_responses = [];
        for($i=0 ; $i < count($events) ; $i++){
            $meeting = new Meeting();
            $meeting->title = $events[$i]->event_name;
            $meeting->start = $events[$i]->event_start_time;
            $meeting->end = $events[$i]->event_end_time;
            $meeting->meeting_code = $events[$i]->event_id;
            $duration = 0;
            $response = $this->getUserActivities_response($request,$email,$events[$i]);
            if($response->responseCode==200){
                $event = json_decode($response->body);
                if(property_exists($event,'items')){
                    $meeting->present = "true";
                    for($j=0;$j<count($event->items);$j++){
                        $item = $event->items[$j];
                        $meeting->time = $item->id->time;
                        if(property_exists($item,'events')){
                            $meeting_events = $item->events;
                            for($h=0;$h<count($meeting_events);$h++){
                                $meeting_event = $meeting_events[$h];
                                $parameters = $meeting_event->parameters;
                                for($k=0;$k<count($parameters);$k++){
                                    if($parameters[$k]->name=="duration_seconds"){
                                        $duration = $duration + intval($parameters[$k]->intValue);
                                    }
                                }
                            }
                        }
                        $meeting->duration = $duration;
                    }
                }else{
                    $meeting->present = "false";
                }
            }
            $activities_responses[] = $meeting;
        }
        return $activities_responses;
    }
}
